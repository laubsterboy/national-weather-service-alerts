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
    <section class="<?php echo $args['heading']['classes']; ?>">
        <!-- Heading graphic -->
        <?php if ($args['heading']['graphic'] !== false && !empty($this->entries)) : ?>
            <?php echo $this->entries[0]->get_output_graphic($args['heading']['graphic'], 'nws-alerts-heading-graphic'); ?>
        <?php endif; ?>

        <!-- Heading location and scope -->
        <?php if (isset($args['widget'])) { ?>
            <?php echo $args['widget_before_title']; ?><span class="nws-alerts-heading-scope"><?php echo $args['heading']['scope']; ?></span><?php echo $args['widget_after_title']; ?>
            <span class="nws-alerts-heading-location"><?php echo $args['heading']['location']; ?></span>
        <?php } else { ?>
            <span class="nws-alerts-heading-location"><?php echo $args['heading']['location']; ?></span><span class="nws-alerts-heading-scope"><?php echo $args['heading']['scope']; ?></span>
        <?php } ?>

        <!-- Heading entry event -->
        <?php echo $args['heading']['alert']; ?>
    </section>
</article>
