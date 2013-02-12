<?php
    defined('M2_MICRO') or die('Direct Access to this location is not allowed.');

    /**
     * Gallery Model class
     * @name $galleryModel
     * @package M2 Micro Framework
     * @subpackage Modules
     * @author Alexander Chaika
     * @since 0.2RC1
     */
    class GalleryModel extends Model {

        /**
         * @var string $file_path
         */
        private $file_path;

        /**
         * @var string $link_path
         */
        private $link_path;

        /**
         * File model constructor
         */
        public function __construct() {
            parent::__construct();

            $this->file_path = realpath(ROOT_PATH . DS . Application::$config['gallery_path']);
            $this->link_path = Application::$config['http_host'] . Application::$config['gallery_path'];
        }

        /**
         * Return gallery array by tags
         * @param array $tags
         * @param int $limit
         * @return array|bool
         */
        public function getGalleryByTags($tags, $limit = 100) {
            if (empty($tags)) {
                return $this->getGallery();
            }

            $tags = implode(',', $tags);
            $this->database->query("CALL GET_GALLERY_BY_TAGS('$tags', $limit)");
            $galleries = $this->database->getObjectsArray();

            return $this->appendGalleriesImages($galleries);
        }

        /**
         * Get gallery list
         * @param int $limit
         * @return array|bool
         */
        public function getGallery($limit = 100) {
            // Get galleries list
            $this->database->query("CALL GET_GALLERY($limit)");
            $galleries = $this->database->getObjectsArray();

            return $this->appendGalleriesImages($galleries);
        }

        /**
         * Get gallery by id
         * @param int $id
         * @return object
         */
        public function getGalleryById($id) {
            // Check empty value
            if (empty($id)) {
                return $this->_throw(T('Gallery ID could not be empty'));
            }

            // Get galleries list
            $this->database->query("CALL GET_GALLERY_BY_ID($id)");
            $galleries = $this->database->getObjectsArray();

            return reset($this->appendGalleriesImages($galleries));
        }

        /**
         * Append galleries images
         * @param array $galleries objects
         * @return array $galleries objects
         */
        private function appendGalleriesImages($galleries) {
            // Append originals and thumbnails
            foreach ($galleries as $gallery) {
                $this->database->query("CALL GET_GALLERY_ITEMS_BY_ID(" . $gallery->id. ")");
                $gallery->originals = $this->database->getObjectsArray();

                // Add originals and thumbnails links
                if (count($gallery->originals)) {
                    foreach($gallery->originals as $original) {
                        $original->link = Application::$config['http_host'] . substr($original->source, 1);
                        $original->thumbnail = Application::$config['http_host'] . substr(str_replace('originals', 'thumbnails', $original->source), 1);
                    }
                }
            }

            return $galleries;
        }

        /**
         * Update gallery entries from FS to DB
         * @return array list of all new images from gallery path
         */
        public function updateFSlist() {
            $file_list = $db_files = array();

            // Get already stored files from DB
            $this->database->query("CALL GET_FILES('gallery', 0);");
            if ($this->database->checkResult()) {
                $db_files = $this->database->getPairs('id', 'source');
            }

            // Get all gallery images
            clearstatcache();
            $fileModel = Model::getModel('file');
            $galleries = $fileModel->getDirList($this->file_path . DS . 'originals', true);
            foreach ($galleries as $path => $data) {
                // Create gallery DB entry
                $dir_name = substr(strrchr($path, '/'), 1);
                $dir_name = $this->database->escape($dir_name);
                $this->database->query("CALL UPSERT_GALLERY(0, '" . $this->database->escape($path) . "', '" . ucfirst($dir_name) . "',  '" . strtolower($dir_name) . "', '', '');");
                $gallery_id = $this->database->getField();

                // Parse files from directory
                foreach ($fileModel->getDirList($path) as $source => $fileinfo) {
                    // If file exists
                    if (realpath(ROOT_PATH . DS . $source)) {
                        // And not already stored in DB
                        if (!in_array($source, $db_files)) {
                            // Add record to files table
                            if ($file_id = $fileModel->add(array('source'=> $source, 'type' => 'gallery',))) {
                                // And add linkage to gallery
                                $this->database->query("CALL UPSERT_GALLERY_FILES(" . $gallery_id . ", " . $file_id . ");");
                                if ($this->database->getField()) {
                                    $file_list[] = array(
                                        'source' => $source,
                                        'status' => T('Stored in DB')
                                    );
                                } else {
                                    $message = $this->getLastFromStack();
                                    $file_list[] = array(
                                        'source' => $source,
                                        'status' => $message['message']
                                    );
                                }
                            } else {
                                $message = $this->getLastFromStack();
                                $file_list[] = array(
                                    'source' => $source,
                                    'status' => $message['message']
                                );
                            }
                        }
                    }
                }
            }

            return $file_list;
        }

        /**
         * Batch resize for gallery originals
         * @return array list of all resized images
         */
        public function rebuildThumbnails() {
            $resized = $db_files = array();

            // Get all registered images
            $this->database->query("CALL GET_FILES('gallery', 1000);");
            if ($this->database->checkResult()) {
                $db_files = $this->database->getPairs('id', 'source');
            }

            // Rebuild thumbnails
            foreach($db_files as $source) {
                // Generate thumbnail pathes
                $pathinfo = pathinfo($source);
                $thumbpath = $this->file_path . DS . 'thumbnails' . strrchr($pathinfo['dirname'], '/');
                $thumbname = $thumbpath . DS . $pathinfo['basename'];

                // Check directory path and try to create it
                if (!file_exists($thumbpath)) {
                    if (!mkdir($thumbpath)) {
                        $message = T('Could not create thumbnail directory');
                        $this->_throw($message);

                        $resized[] = array(
                            'source' => $thumbpath,
                            'status' => $message
                        );

                        continue;
                    }
                }

                // Check thumbnail and try to create it
                if (!file_exists($thumbname)) {
                    if (System::getInstance()->resize($source, $thumbname, Application::$config['thumb_width'], Application::$config['thumb_height'])) {
                        $resized[] = array(
                            'source' => $source,
                            'status' => T('Successfully resized')
                        );
                    } else {
                        $message = $this->getLastFromStack();
                        $resized[] = array(
                            'source' => $source,
                            'status' => $message['message']
                        );
                    }
                }
            }

            return $resized;
        }



        /**
         * Track gallery by id
         * @param int $id
         * @return bool|object $result
         */
        public function trackGallery($id){
            // Check empty value
            if (empty($id)) {
                return $this->_throw(T('Gallery ID could not be empty'));
            }

            $this->database->query("CALL TRACK_GALLERY_BY_ID($id);");
            return $this->database->getField();
        }
    }