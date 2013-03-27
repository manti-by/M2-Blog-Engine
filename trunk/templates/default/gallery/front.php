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
     * Frontpage gallery block template
     * @name $front-gallery
     * @package M2 Micro Framework
     * @subpackage Template
     * @author Alexander Chaika
     * @since 0.3RC4
     */

    $count = 0;
    $in_use = array();
?>
<div id="gallery">
    <h2 class="with-full-link">
        <a href="<?php echo Sef::getSef('index.php?module=gallery'); ?>">
            <?php echo T('Latest shots from gallery'); ?>
        </a>
        <div class="fr view-all">
            <a href="<?php echo Sef::getSef('index.php?module=gallery'); ?>">
                <?php echo T('Show all'); ?>
            </a>
        </div>
    </h2>
    <div class="latest fl">
        <?php foreach ($options['data']['gallery_latest'] as $original) : ?>
            <?php
                // Check preview count
                if ($count >= Application::$config['front_count']) break;
                else $count++;

                // Add used id
                $in_use[] = $original->id;
            ?>

            <a href="<?php echo Sef::getSef('index.php?module=gallery'); ?>#image-<?php echo $original->id; ?>" class="thumbnail" rel="<?php echo $original->id; ?>">
                <img src="<?php echo Application::$config['http_host'] . substr(str_replace('originals', 'thumbnails', $original->source), 1); ?>" width="100" height="100" />
            </a>
        <?php endforeach; ?>
    </div>

    <?php
        // Skip used popular images
        $popular_image = reset($options['data']['gallery_popular']);
        foreach (array_reverse($options['data']['gallery_popular']) as $popular) {
            if (!in_array($popular->id, $in_use)) $popular_image = $popular;
        }
    ?>

    <div class="popular fl">
        <a href="<?php echo Sef::getSef('index.php?module=gallery'); ?>#image-<?php echo $popular_image->id; ?>" rel="<?php echo $popular_image->id; ?>">
            <img src="<?php echo  Application::$config['http_host'] . substr($popular_image->source, 1); ?>" />
        </a>
    </div>
    <div class="cls"></div>
</div>