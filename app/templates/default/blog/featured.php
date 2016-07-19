<?php
    /**
     * M2 Micro Framework - a micro PHP 5 framework
     *
     * @author      Alexander Chaika <marco.manti@gmail.com>
     * @copyright   2012 Alexander Chaika
     * @link        https://github.com/marco-manti/M2_micro
     * @version     0.3
     * @package     M2 Micro Framework
     * @license     https://raw.github.com/marco-manti/M2_micro/manti-by-dev/NEW-BSD-LICENSE
     *
     * NEW BSD LICENSE
     *
     * All rights reserved.
     *
     * Redistribution and use in source and binary forms, with or without
     * modification, are permitted provided that the following conditions are met:
     *  * Redistributions of source code must retain the above copyright
     * notice, this list of conditions and the following disclaimer.
     *  * Redistributions in binary form must reproduce the above copyright
     * notice, this list of conditions and the following disclaimer in the
     * documentation and/or other materials provided with the distribution.
     *  * Neither the name of the "M2 Micro Framework" nor "manti.by" nor the
     * names of its contributors may be used to endorse or promote products
     * derived from this software without specific prior written permission.

     * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
     * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
     * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
     * DISCLAIMED. IN NO EVENT SHALL COPYRIGHT HOLDER BE LIABLE FOR ANY
     * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
     * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
     * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
     * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
     * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
     * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
     */

    defined('M2_MICRO') or die('Direct Access to this location is not allowed.');

    /**
     * Featured block template
     * @name $post-item
     * @author Alexander Chaika
     * @package M2 Micro Framework
     * @subpackage Template
     * @since 0.3RC1
     * @todo Create featured slider
     */

    $active = false;
    $count = 0;
?>
<div id="featured">
    <?php if (count($options['data'])) : ?>
        <ul id="slider">
            <?php foreach($options['data'] as $item) : ?>
                <?php if (json_decode($item->covers)) : ?>
                    <li class="slider-item<?php echo $active ? ' active' : ''; ?>">
                        <div class="wrap post-item">
                            <div class="cover">
                                <a href="<?php echo Sef::getSef('index.php?module=blog&action=show&id=' . $item->id); ?>">
                                    <?php echo File::getHtml($item->covers, FileEntity::TYPE_COVERS); ?>
                                </a>
                            </div>

                            <div class="info">
                                <h3>
                                    <a href="<?php echo Sef::getSef('index.php?module=blog&action=show&id=' . $item->id); ?>">
                                        <?php echo $item->name . (!empty($item->genre) ? ' /' . $item->genre.  '/' : ''); ?>
                                    </a>
                                </h3>

                                <div id="teaser-<?php echo $item->id; ?>" class="teaser">
                                    <?php echo nl2br($item->teaser); ?>
                                </div>

                                <?php
                                    echo $this->getContents('blog', 'featured-music-block', array('data' => $item));
                                ?>
                            </div>

                            <?php if ($item->tracklist) : ?>
                                <div id="tracklist-<?php echo $item->id; ?>" class="tracklist hidden">
                                    <?php echo nl2br($item->tracklist); ?>
                                    <div class="links">
                                        <a href="#hide-tracklist" class="hide-tracklist">
                                            <?php echo T('Hide tracklist'); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php $active = (++$count == 2 ? true : false); ?>
                        </div>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <?php echo T('There are no contents found'); ?>
    <?php endif; ?>
</div>