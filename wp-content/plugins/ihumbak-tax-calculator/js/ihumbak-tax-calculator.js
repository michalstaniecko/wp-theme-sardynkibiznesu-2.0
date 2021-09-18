(function ($) {

	function excludedKey(keyCode) {
		var isExcluded = false;
		var excludedKey = [8,46,37,39];
		excludedKey.forEach(function(element, index) {
			if (element == keyCode) {
				isExcluded = true;
			}
		});
		return isExcluded;
	}

	$('#tax__amount').on('keyup', function(event){
		var patt = /^[0-9]*([\.\,][0-9]*)?$/g;
		var amount = $(this).val();
		if (patt.test(amount)){
			$(this).removeClass('tax__error');
			$('#tax__submit').prop('disabled', false);
		} else {
			$(this).addClass('tax__error');
			$('#tax__submit').prop('disabled', true);
		}
		/*
		if (event.type=="keyup") {
			var patt = new RegExp("[0-9]([\.\,][0-9])?");
			if (patt.test(event.key)) {

			}
		}
		if ($(this).val().search(/[\.\,]/g) != -1) {

			var patt = new RegExp("[^0-9]");
			if (patt.test(event.key) && !excludedKey(event.keyCode)) {
				event.preventDefault();
				event.stopPropagation();
				return false;
			}
		} else {

			var patt = new RegExp("[^0-9\,\.]");
			if (patt.test(event.key) && !excludedKey(event.keyCode)) {
				event.preventDefault();
				event.stopPropagation();
				return false;
			}
		}

		 */
	});

	$('#tax__submit').on('click', function (e) {
		e.preventDefault();
		var amount = $('#tax__amount').val().replace(',','.');
		var amountType = parseInt($('#tax__amount-type').val());
		var vatPercent = parseInt($('#tax__vat-percent').val());
		var pitPercent = parseInt($('#tax__pit-percent').val());
		var bruttoAmount = calcBrutto(amount, amountType, vatPercent, pitPercent);
		var nettoAmount = calcNetto(bruttoAmount,vatPercent);
		var afterDeductionAmount = calcAfterDeduction(nettoAmount, pitPercent);
		var vatAmount = roundNumber(nettoAmount * (vatPercent/100),2);
		var pitAmount = roundNumber(nettoAmount * (pitPercent/100),2);
		console.log(amount, amountType, vatPercent, pitPercent, bruttoAmount, nettoAmount,afterDeductionAmount);
		renderResults(bruttoAmount, nettoAmount, vatAmount, pitAmount, afterDeductionAmount);
	});

	function renderResults(brutto, netto, vat, pit, afterDeducation) {
		$('#tax__brutto-amount').text(brutto.toString().replace('.',','));
		$('#tax__netto-amount').text(netto.toString().replace('.',','));
		$('#tax__vat-amount').text(vat.toString().replace('.',','));
		$('#tax__pit-amount').text(pit.toString().replace('.',','));
		$('#tax__after-tax-deduction').text(afterDeducation.toString().replace('.',','));
	}

	function calcAfterDeduction(netto, pit){
		return roundNumber(netto * (1 - pit/100),2);
	}
	function calcNetto(brutto, vat) {
		return roundNumber(brutto/(1 + vat/100) ,2);
	}

	function calcBrutto(amount, type, vat, pit) {
		switch (type) {
			case 1:
				return roundNumber(amount,2);
				break;
			case 2:
				return roundNumber(amount * (1 + vat / 100),2);
				break;
			case 3:
				return roundNumber((amount / (1 - pit / 100)) * (1 + (vat / 100)),2);
				break;
			default:
				return false;
				break;
		}
	}

	function roundNumber(num, scale) {
		if(!("" + num).includes("e")) {
			return +(Math.round(num + "e+" + scale)  + "e-" + scale);
		} else {
			var arr = ("" + num).split("e");
			var sig = ""
			if(+arr[1] + scale > 0) {
				sig = "+";
			}
			return +(Math.round(+arr[0] + "e" + sig + (+arr[1] + scale)) + "e-" + scale);
		}
	}
})(jQuery);