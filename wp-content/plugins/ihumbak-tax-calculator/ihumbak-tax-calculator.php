<?php

/*
 * Plugin Name: iHumbak Tax Calculator
 * Version: 0.4
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/*
include_once plugin_dir_path( __FILE__ ) . 'updater.php';
*/

class IHumbak_Tax_Calculator {
  public function __construct() {

    add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );

    $this->shortcodes_init();
  }

  function assets() {
    wp_enqueue_script( 'ihumbak-tax-calculator', plugin_dir_url( __FILE__ ) . 'js/ihumbak-tax-calculator.js', array(
      'jquery'
    ), 0.4, true );
    //wp_enqueue_script( 'jquery-mask', plugin_dir_url( __FILE__ ) . 'js/jQuery-Mask-Plugin-master/dist/jquery.mask.min.js', array( 'jquery' ), null, true );
    //wp_enqueue_script( 'RobinHerbots-Inputmask', plugin_dir_url( __FILE__ ) . 'js/RobinHerbots-Inputmask/dist/jquery.inputmask.min.js', array( 'jquery' ), null, true );
    wp_enqueue_style( 'ihumbak-tax-calculator', plugin_dir_url( __FILE__ ) . 'css/ihumbak-tax-calculator.css' );
  }

  function shortcodes_init() {
    add_shortcode( 'ihumbak_tax_calculator', array( $this, 'render' ) );
  }

  function render( $atts, $content ) {
    ob_start();
    ?>
    <p>
      Kalkulator pozwala obliczyć kwotę VAT oraz cenę netto i brutto.
    </p>
    <ol class="tax__instruction">
      <li>Wpisz kwotę, od której chcesz policzyć VAT</li>
      <li>Określ typ wprowadzonej kwoty: butto czy netto</li>
      <li>Wybierz stawkę podatku VAT</li>
      <li>Kliknij "Policz"</li>
    </ol>
    <form>
      <div class="tax__row">
        <div class="tax__col">
          <label for="tax__amount">Kwota</label>
        </div>
        <div class="tax__col">

          <input id="tax__amount" class="tax__amount" name="tax__amount" type="text"/>
          <div class="tax__hint">

            W formacie 123,45
          </div>
        </div>
      </div>
      <div class="tax__row">
        <div class="tax__col">

          <label for="tax__amount-type">Typ kwoty</label>
        </div>
        <div class="tax__col">

          <select name="tax__amount-type" id="tax__amount-type">
            <option value="1">Brutto</option>
            <option value="2">Netto</option>
          </select>
        </div>
      </div>
      <div class="tax__row">
        <div class="tax__col">

          <label form="tax__vat-percent">
            Podatek VAT
          </label>
        </div>
        <div class="tax__col">

          <select name="tax__vat-percent" id="tax__vat-percent">
            <option value="23">
              23%
            </option>
            <option value="8">
              8%
            </option>
            <option value="5">
              5%
            </option>
          </select>
        </div>
      </div>
      <div class="tax__row" style="display:none">
        <div class="tax__col">

          <label for="tax__pit-percent">
            Podatek PIT
          </label>
        </div>
        <div class="tax__col">

          <select id="tax__pit-percent" name="tax__pit-percent">
            <option value="18">
              18%
            </option>
            <option value="19">
              19%
            </option>
          </select>
        </div>
      </div>
      <div class="tax__row-submit">

        <button type="submit" id="tax__submit" class="tax__submit" disabled>
          Policz
        </button>
      </div>
    </form>
    <div class="tax__results">
      <div class="tax__row">

        <div class="tax__col">
          Brutto
        </div>
        <div class="tax__col">
          <span id="tax__brutto-amount"></span> zł
        </div>
      </div>
      <div class="tax__row">
        <div class="tax__col">

          Netto
        </div>
        <div class="tax__col">
          <span id="tax__netto-amount"></span> zł
        </div>
      </div>
      <div class="tax__row">
        <div class="tax__col">

          Wartość podatku VAT
        </div>
        <div class="tax__col">
          <span id="tax__vat-amount"></span> zł
        </div>
      </div>
      <div style="display:none">

        <div class="tax__row">
          <div class="tax__col">

            Wartość podatku PIT
          </div>
          <div class="tax__col">
            <span id="tax__pit-amount"></span> zł
          </div>
        </div>
        <div class="tax__row">
          <div class="tax__col">

            Po odliczeniu VAT i PIT
          </div>
          <div class="tax__col">
            <span id="tax__after-tax-deduction"></span> zł
          </div>
        </div>
      </div>

    </div>

    <?php
    return ob_get_clean();

  }
}

new IHumbak_Tax_Calculator();
