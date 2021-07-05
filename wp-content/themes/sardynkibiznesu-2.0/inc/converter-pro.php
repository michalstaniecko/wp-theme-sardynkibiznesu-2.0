<?php
add_action('cppro_load_popup_data', 'sb_cppro_load_popup_data', 10 , 1);
function sb_cppro_load_popup_data($is_popup_live) {
  return true;
}
