<?php
// create custom plugin settings menu
add_action('admin_menu', 'ihumbak_plugin_create_menu');

function ihumbak_plugin_create_menu() {

  //create new top-level menu
  add_menu_page('iHumbak Options', 'iHumbak Plugins', 'administrator', 'ihumbak-options', 'ihumbak_options_page'  );
  add_submenu_page('ihumbak-options','iHumbak Anchor Options', 'Anchor','administrator','ihumbak-anchor','ihumbak_anchor_settings_page');
  //call register settings function
  add_action( 'admin_init', 'register_ihumbak_plugin_settings' );
}

function ihumbak_anchor_settings_page() {
  ?>
  <div class="wrap">
    <h1>iHumbak Anchor Options</h1>
  </div>
  <form method="post" action="options.php">
    <?php settings_fields( 'ihumbak-anchor-settings-group' ); ?>
    <?php do_settings_sections( 'ihumbak-anchor-settings-group' ); ?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">CSS styles</th>
        <td><textarea style="width: 100%; height: 350px;" type="text" name="ihumbak_anchor_styles" ><?php echo esc_attr( get_option('ihumbak_anchor_styles') ); ?></textarea></td>

      </tr>

    </table>

    <?php submit_button(); ?>

  </form>
  <?php
}


function register_ihumbak_plugin_settings() {
  //register our settings
  register_setting( 'ihumbak-anchor-settings-group', 'ihumbak_anchor_styles' );
}

function ihumbak_options_page() {
  ?>
  <div class="wrap">
    <h1>iHumbak Plugins</h1>

  </div>
<?php } ?>