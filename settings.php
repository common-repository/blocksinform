<style>
    .blocksinform-container {
        padding: 20px;
        padding-left: 0;
        width: 420px;
        font-size: 14px;
    }
    table {
        width: 400px;
        margin-left: 20px;
    }
    table td img {
        position: relative;
        top: 3px;
        margin-left: 5px;
    }
        /*    table {
                padding: 12px;
                border: 3px solid #467FD7;
                border-radius: 5px;
                margin-top: 10px;
                width: 100%;
            }*/
    table tr td:first-child {
        width: 110px;
    }
    table input {
        width: 100%;
    }
    input[type='text'] {
        border-radius: 3px;
    }
    hr {
        margin-top: 15px;
        margin-bottom: 15px;
        border-bottom: none;
    }
    input[type='checkbox'] {

    }
    input[type='submit']{
        float: right;
    }
    .checkbox {
        margin-bottom: 10px;
    }
    .request_link {
        float: right;
        margin-right: 10px;
        line-height: 26px;
    }
    table .tooltip {
        /*        position:relative;
                top:50px;
                left:50px;*/
    }
    .tooltip div { /* hide and position tooltip */
        background-color: black;
        color: white;
        border-radius: 5px;
        opacity: 0;
        position: absolute;
        -webkit-transition: opacity 0.5s;
        -moz-transition: opacity 0.5s;
        -ms-transition: opacity 0.5s;
        -o-transition: opacity 0.5s;
        transition: opacity 0.5s;
        width: 200px;
        padding: 10px;
        margin-left: 30px;
        margin-top: -45px;
    }
    table .tooltip:hover div { /* display tooltip on hover */
        opacity:1;
    }
    .label-success {
        font-size: 17px;
        display: block;
        margin-top: 10px;
        color: green;
    }
    .label-error {
        font-size: 17px;
        display: block;
        margin-top: 10px;
        color: red;
    }

    .toggle_icon{
        background: url(<?php echo esc_url($this->plugin_url).'img/arrow_right_32.png' ?>) no-repeat;
        background-size:contain;
        width:24px;
        height:24px;
        display:inline-block;
        vertical-align: middle;
    }

    .toggle_icon_on{
        -ms-transform: rotate(90deg); /* IE 9 */
        -webkit-transform: rotate(90deg); /* Chrome, Safari, Opera */
        transform: rotate(90deg);
    }
    .apply_button,.stat_button{
        margin-top: 20px !important;
    }
    .stat_button
    {
        float:left;
    }

</style>

<div class="blocksinform-container">
    <img src='<?php echo esc_url($this->plugin_url).'img/blocksinform.png' ?>' style='width:64px;'/>
    <hr>

    <form method="POST">
        <table>
            <tr>
                <td>Publisher ID</td>
                <td>
                    <input type="text" name="publisher_id" placeholder="Input publisher ID" value="<?php echo !empty($settings->publisher_id) ? esc_html($settings->publisher_id) : '' ?>"/>
                </td>
                <td class='tooltip'>
                    <img src='<?php echo esc_url($this->plugin_url).'img/question-mark.png' ?>'/>
                    <div>Please contact your BlocksInform representative to receive the Publisher ID</div>
                </td>
            </tr>
            <tr>
                <td colspan='2' style='line-height: 26px; font-size: 13px;'>
                    Don't have a Publisher ID? Register 
                    <a style='float: inherit; margin-left:5px;' class='request_link' href=' https://my.blocksinform.com/reg_acc?website=https://<?php echo parse_url( get_site_url(), PHP_URL_HOST )  ?>' target='_blank'>BlocksInform Account</a>
                        Where get Publisher ID?. <a style='float: inherit; margin-left:5px;' class='request_link' href=' https://doc.blocksinform.com/wordpress-plugin' target='_blank'>Look the page</a>
                </td>
            </tr>
        </table>

        <hr style='margin-bottom: 25px; margin-top: 5px;'>

        <div class='checkbox'>
            <input id="below_enabled" type="checkbox" <?php echo !empty($settings->below_enabled) ? "checked='checked'" : "" ?> name="below_enabled"/>
            Below Article
        </div>
        <hr>

        <div class='checkbox'>
            <input id="sidebar_enabled" type="checkbox" <?php echo !empty($settings->sidebar_enabled) ? "checked='checked'" : "" ?> name="sidebar_enabled"/>
            Sidebar Article
        </div>
        <?php if(!empty($settings->publisher_id)):?>
        <a target='_blank' href='//my.blocksinform.com' class='button-secondary stat_button'>📊 Statistic</a><?php endif; ?><input class='button-secondary apply_button' type="submit" value="Apply Changes ✔"/>
    </form>
    <div style='clear:both'></div>

    
</div>
