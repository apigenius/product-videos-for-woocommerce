<?php

// If this file is called directly, abort.
if (! defined('WPINC')) {
   die;
}

?>
<style>
    .api-table, .api-video-div {
        max-width: 700px;
        width: 100%;
        display: block;
        margin: 50px auto;
        padding: 10px;
        background: #fff;
        border: 1px solid #d8d8d8;
    }
    h2 {
        text-align: center;
    }
    .button {
        text-align: center;
        display: block;
    }
</style>

<h1 style="text-align: center;"><?php echo esc_html( get_admin_page_title() ); ?></h1>

<!-- get support -->
<table class="api-table">
    <tr>
        <td><a class="button" href="https://www.apigenius.io/my-tickets/submit-ticket/" target="_blank">Create Help Ticket</a></td>
        <td style="padding-left: 25px;"><p>Thank you for using the Product Videos for Woocommerce Plugin.  If you have any issues, please give us an opportunity to assist you by opening a support ticket.<p></td>
    </tr>
</table>

<!-- display help video -->
<div class="api-video-div">
    <iframe width="100%" height="400" src="https://www.youtube.com/embed/sXB3XSAUOgo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</div>

<h2>Common Errors</h2>
<table class="api-table">
    <tr>
        <td></td>
    </tr>
</table>
