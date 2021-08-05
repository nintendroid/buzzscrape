<?php

require_once(__DIR__ . '/cache.php');

/**
 * Buzzscrape widget class
 */
class BZS_Widget extends WP_Widget {
    private const DOMAIN = 'wpb_domain';
    
    function __construct() {
        parent::__construct(
            'bzs_widget', // base ID
            __('Buzzsprout Scraper', self::DOMAIN), // name
            array(
                'description' => __('Widget for listing podcast services', self::DOMAIN)
            ) 
        );
    }
    
    // Creates the widget UI
    public function widget( $args, $props ) {
        $result = bzs_find_channels($props['bid']);
        $cls = empty($result) ? 'bzs-empty' : '';
?>
<div class="widget bzs-list-container <?php echo $cls; ?>">
    <p class="widget-title"><?php echo __('Listen to our podcast on', self::DOMAIN) ?></p>
    <div class="bzs-status"></div>
    <ul>
<?php if (!empty($result)) {
        foreach ($result as $row) { ?>
        <li class="bzs-item-<?php echo empty($row->cid) ? 'other' : $row->cid; ?>"
            style="background-image: url(<?php echo plugins_url('img/icons.svg', __FILE__); ?>);"
        >
            <a href="<?php echo $row->url; ?>" target="_blank"><?php echo $row->cname; ?></a>
        </li>
<?php } /* end foreeach */ } /* end if */ ?>
    </ul>
</div>

<?php
    }

    // Creates the admin UI
    public function form($props) {
        $bid = isset($props['bid']) ? $props['bid'] : '';
        $bidField = $this->get_field_id('bid');
        $bidName = $this->get_field_name('bid');

        $adminUrl = admin_url('admin-ajax.php');
        $checkState = get_option('bzs_auto_refresh');

        // Widget admin form
        ?>
<div class="bsz-admin-container">
    <fieldset>
        <p>
            <label for="<?php echo $bidField; ?>">
                <?php __('Buzzsprout ID:', self::DOMAIN); ?>
            </label> 
            <input class="code" type="text"
                id="<?php echo $bidField; ?>" name="<?php echo $bidName; ?>"
                value="<?php echo esc_attr($bid); ?>"
            />
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php if ($checkState === 'on') { echo 'checked'; } ?>
                id="bzs-autorefresh" onchange="adminEnableRefresh('<?php echo $adminUrl ?>', 'bzs-autorefresh');"
            />
            <label for="bzs-autorefresh">Enable automatic refresh</label>
        </p>
    </fieldset>
    <p><button class="components-button is-primary"
        onclick="adminRefreshCache('<?php echo $adminUrl ?>', '<?php echo $bidField; ?>');"
    >Refresh</button></p>
</div>
        <?php 
    }
        
    // Commit admin changes
    public function update($newProps, $oldProps) {
        $props = $newProps;
        return $props;
    }
    
} // end BZS_Widget
