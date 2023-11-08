// assets/bo/category.js
import "../../lib/js/dropzone.js";
import "./cropper.js";

// import 'select2';
// import '../../lib/js/select';
import Cropper from 'cropperjs';



(function() {'use strict';
    console.info('welcome to projets.js');

    // ! Variables

    // Sélecteur principal de la vue.
    let $main                 =     $('body');
    let tableTotalSelector   =     '.table-total';
    let $tableTotal          =     $main.find(tableTotalSelector);

    let totalMontantHT  = 0;
    let totalMontantTTC = 0;


    // When DOM is ready.
    $(document).ready(function() {
        // Des images et l'action de crop est présent.
        $tableTotal.find('td.total-ht').prev().text('TOTAL');

        //Calcul du montant total HT et TTC
        $main.find('.table.datagrid tbody tr').each(function(index, element) {
            if( ! $(this).hasClass('table-total')) {
                let $tr         = $(this);
                let $tdHT = $tr.find('td input.montant-ht');
                let montantHT  = $tdHT.parent().text();
                // On enlève les espaces et on remplace les virgules par des points
                montantHT      = montantHT.replace(/\s/g, '');
                montantHT      = montantHT.replace(/,/g, '.');
                if(montantHT.length > 0) {
                    totalMontantHT += parseFloat(montantHT);
                }
                let $tdTTC = $tr.find('td input.montant-ttc');
                let montantTTC = $tdTTC.parent().text();
                // On enlève les espaces et les virgules
                montantTTC = montantTTC.replace(/\s/g, '');
                montantTTC = montantTTC.replace(/,/g, '.');
                if(montantTTC.length > 0) {
                    totalMontantTTC += parseFloat(montantTTC);
                }
            }
        });
        let euro = Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
            useGrouping: true,
        });

        // On formate le montant total HT
        $tableTotal.find('td.total-ht').text(euro.format(totalMontantHT));
        $tableTotal.find('td.total-ttc').text(euro.format(totalMontantTTC));


        // Active ou désactive les étapes à cocher du projet on fonction de celle qui vient d'être cochée ou décochée
        $main.on('change', '.table.datagrid tbody .form-check input',function(event) {
            // Case cochée
            let $input = $(this);
            // Case précedente
            let $prevInput = $input.parents('td').prev().find('input');
            // Case suivante
            let $nextInput = $input.parents('td').next().find('input');
            // Si la case est cochée
            if($input.is(':checked')) {
                // On désactive la case précedente
                $prevInput.attr('disabled', true);
                // On active la case suivante
                $nextInput.attr('disabled', false);
            }
            // Si la cace est décochée
            else {
                // Si la case précédente possède un identifiant d'utilisateur
                if($prevInput.attr('data-user-id')) {
                    // Si cet identifiant correspond à l'utilisateur connecté
                    if($prevInput.attr('data-user-id') === $prevInput.attr('data-current-user-id')) {
                        // On active la case précedente
                        $prevInput.attr('disabled', false);
                    }
                }
                // Sinon c'est qu'on est administrateur
                else{
                    // On active la case précedente
                    $prevInput.attr('disabled', false);
                }
                // On désactive la case suivante
                $nextInput.attr('disabled', true);
            }
        });

    });
})();