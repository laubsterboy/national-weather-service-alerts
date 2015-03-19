<?php

/**
* National Weather Service Alerts uses template files to manage html output.
* Each template in the "template" plugin directory can be overridden and
* customized by copying it to your theme directory, such as the paths below:
*
*   /themes/{child-theme-name}/plugins/national-weather-service-alerts/templates/{template-name.php}
*   /themes/{parent-theme-name}/plugins/national-weather-service-alerts/templates/{template-name.php}
*
*/

?>

<article class="nws-alerts <?php echo $classes; ?>" data-settings="<?php echo $settings; ?>">
    <!-- Heading -->
    <section class="<?php echo $args['heading']['classes']; ?> nws-alerts-heading-no-graphic">
        <!-- Heading location and scope -->
        <?php if (isset($args['widget'])) { ?>
            <?php echo $args['widget_before_title']; ?><span class="nws-alerts-heading-scope"><?php echo $args['heading']['scope']; ?></span><?php echo $args['widget_after_title']; ?>
            <span class="nws-alerts-heading-location"><?php echo $args['heading']['location']; ?></span>
        <?php } else { ?>
            <span class="nws-alerts-heading-location"><?php echo $args['heading']['location']; ?></span><span class="nws-alerts-heading-scope"><?php echo $args['heading']['scope']; ?></span>
        <?php } ?>
    </section>

    <!-- Details -->
    <section class="nws-alerts-details">
        <!-- Details entries -->
        <section class="nws-alerts-entries">
        <?php if ($this->error) : ?>
            <!-- Entries error -->
            <?php echo $this->error; ?>
        <?php elseif (!empty($this->entries)) : ?>
            <!-- Entries -->
            <ul>
            <?php foreach ($this->entries as $entry) { ?>
                <li><?php echo $entry->get_output_entry(); ?></li>
            <?php } ?>
            </ul>
        <?php else : ?>
            <!-- Entries empty -->
            <?php echo NWS_ALERTS_ERROR_NO_ENTRIES; ?>
        <?php endif; ?>
        </section>

        <!-- Details map -->
        <?php echo $this->get_output_google_map(); ?>
    </section>
</article>
