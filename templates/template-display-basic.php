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

<article class="nws-alerts <?php echo trim(implode(' ', $classes)); ?>" data-zip="<?php echo $this->zip; ?>" data-display="<?php echo $display; ?>" data-scope="<?php echo $this->scope; ?>" data-refresh_rate="<?php echo $this->refresh_rate; ?>">
    <!-- Heading -->
    <section class="<?php echo trim(implode(' ', $heading_args['classes'])); ?>">
        <!-- Heading graphic -->
        <?php if ($heading_args['graphic'] !== false && !empty($this->entries)) : ?>
            <?php echo $this->entries[0]->get_output_graphic($heading_args['graphic'], 'nws-alerts-heading-graphic'); ?>
        <?php endif; ?>

        <!-- Heading location and scope -->
        <span class="nws-alerts-heading-location"><?php echo $heading_args['location']; ?></span><span class="nws-alerts-heading-scope"><?php echo $heading_args['scope']; ?></span>

        <!-- Heading entry event -->
        <?php echo $heading_args['alert']; ?>
    </section>
</article>
