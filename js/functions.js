/**
 *
 * @param field
 * @param type
 *
 * Esta función es usada por checkNoEmpty y comprueba que un campo requerido se haya completado,
 * en caso contrario aplicaría un estilo al elemento (border_error) o (border_ok)
 *
 */

var completeRequiredFields = 'Please complete the required fields!';

$(document).ready(function() {
    if ($('#js_required_fields').length > 0) {
        completeRequiredFields = $('#js_required_fields').val();
    }
});

function checkField(field) {
    if (jQuery(comprobate[i])[0].tagName == 'SELECT') { // Si es un select se comprueba que sea distinto de 0
        if (jQuery(field).val() == "" ) {
            if($(field).hasClass("form-control-success")) {
                $(field).removeClass("form-control-success")
            }
            border_error(field);
        } else {
            border_ok(field);
        }
    } else {
        if(jQuery(field).val() == "") {
            if($(field).hasClass("form-control-success")) {
                $(field).removeClass("form-control-success");
                $(field + "_response").html("");
            }

            border_error(field);
        } else {
            border_ok(field);
        }
    }
}

/**
 *
 * @param comprobate
 * @returns {boolean}
 *
 * Comprueba que se hayan completado los campos obligatorios de un formulario
 */
function checkNoEmpty(comprobate) {
    // Número de valores
    n = comprobate.length;
    ok = 0;

    for (i = 0; i < comprobate.length; i++) {
        if (jQuery(comprobate[i]).length == 0) {
            alert("No existe el campo a comprobar '" + comprobate[i] + "'");
            return false;
        } else {
            checkField(comprobate[i]); // comprueba cada campo, si no ha sido completado le aplica un borde de color rojo

            if (jQuery(comprobate[i])[0].tagName == 'SELECT') {
                //if (jQuery(comprobate[i]).val() > 0 || jQuery(comprobate[i]).val() == -1 || jQuery(comprobate[i]).val() != "") {
                if (jQuery(comprobate[i]).val() != "") {
                    ok++;
                }
            } else {
                if (jQuery(comprobate[i]).val() != "") {
                    ok++;
                }
            }
        }
    }

    if (n == ok) {
        return true;
    }

    return false;
}

function border_error(id) {
    jQuery(id).css('border','1px solid red');
}

function border_ok(id) {
    jQuery(id).css('border', '1px solid #DDDDDD');
}

function addError(div, field) {
    $(div).removeClass("has-success");
    $(field).removeClass("form-control-success");
    $(div).addClass("has-danger");
    $(field).addClass("form-control-danger");
}

function addSuccess(div, field) {
    $(div).removeClass("has-danger");
    $(field).removeClass("form-control-danger");
    $(div).addClass("has-success");
    $(field).addClass("form-control-success");
}

function scrollingTo(id) {
    $('html,body').animate({
            scrollTop: $(id).offset().top},
        'slow');
}

function validar(string) { // Borra los caracteres no numéricos entrados en tiempo real
    for (var i=0, output='', validos="0123456789."; i<string.length; i++){
        if (validos.indexOf(string.charAt(i)) != -1) {
            output += string.charAt(i);
        }
    }
    return output;
}

function validarFecha(string) { // Borra los caracteres no numéricos entrados en tiempo real
    for (var i=0, output='', validos="0123456789/"; i<string.length; i++){
        if (validos.indexOf(string.charAt(i)) != -1){
            output += string.charAt(i);
        }
    }
    return output;
}

function validarNumeros(string) { // Borra los caracteres no numéricos entrados en tiempo real
    for (var i=0, output='', validos="0123456789"; i<string.length; i++) {
        if (validos.indexOf(string.charAt(i)) != -1) {
            output += string.charAt(i);
        }
    }
    return output;
}

function redirect(url) {
    location.href=url;
}

/**
 * Funcion que devuelve un numero separando los separadores de miles
 * Puede recibir valores negativos y con decimales
 *
 Ejemplos de uso:

 formatNumber.new(123456779.18, "$") // retorna "$123.456.779,18"
 formatNumber.new(123456779.18) // retorna "123.456.779,18"
 formatNumber.new(123456779) // retorna "$123.456.779"
 **/

var formatNumber = {
    separador: ".", // separador para los miles
    sepDecimal: ',', // separador para los decimales
    formatear:function (num) {
        num +='';
        var splitStr = num.split('.');
        var splitLeft = splitStr[0];
        var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
        var regx = /(\d+)(\d{3})/;
        while (regx.test(splitLeft)) {
            splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
        }
        return this.simbol + splitLeft  +splitRight;
    },
    new:function(num, simbol) {
        this.simbol = simbol ||'';
        return this.formatear(num);
    }
}

/**
 *
 * @param number
 * @returns {string}
 *
 * Funcion que formatea un numero separandolo por miles
 */

function numberFormat(input) {
    var num = input.value.replace(/\./g,'');

    if (!isNaN(num)) {
        num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
        num = num.split('').reverse().join('').replace(/^[\.]/,'');
        input.value = num;
    } else {
        input.value = input.value.replace(/[^\d\.]*/g,'');
    }
}

/**
 * Login de usuario
 * @returns {boolean}
 */
function check_login() {
    comprobate = Array('#user_login','#user_pass');

    if (checkNoEmpty(comprobate)) {

        return true;
    } else {
        completeRequiredFields = $('#js_required_fields').val();
        alert(completeRequiredFields);

        return false;
    }
}

function reloadQtip() {
    // Cambia los titles de los enlaces por qtips (titulos más bonitos)
    $( document ).ready(function() {
        // This will automatically grab the 'title' attribute and replace
        // the regular browser tooltips for all <a> elements with a title attribute!
        $('a[title].withqtip').qtip({
            content: {
                title: {
                    //button: 'Close' // Close button
                }
            }/*,

             show: {
             solo: true
             },
             hide: false // Don't hide on any event except close button*/
        });


        // MAKE SURE YOUR SELECTOR MATCHES SOMETHING IN YOUR HTML!!!
        $('a.qtiphtml').each(function() {
            $(this).qtip({
                content: {
                    text: function(event, api) {
                        $.ajax({
                            url: api.elements.target.attr('href') // Use href attribute as URL
                        })
                            .then(function(content) {
                                // Set the tooltip content upon successful retrieval
                                api.set('content.text', content);
                            }, function(xhr, status, error) {
                                // Upon failure... set the tooltip content to error
                                api.set('content.text', status + ': ' + error);
                            });

                        return 'Loading...'; // Set some initial text
                    }
                },
                position: {
                    viewport: $(window)
                },
                style: 'qtip-wiki'
            });
        });

        $('td[title].withqtip-no-close').qtip({
            content: {
                title: {
                    //button: 'Close' // Close button
                }
            }
        });

        $('tr[title].trwithqtip').qtip({
            content: {
                title: {
                    //button: 'Close' // Close button
                }
            },
            position: {
                my: 'center',
                at: 'center',
                target: $(window) // Or $(document.body), if you don't want it centered as you scroll
            }
        });

        $('a[title].withqtip-no-close').qtip({
            content: {
                title: {
                   // button: 'Close' // Close button
                }
            },
            //hide: false // Don't hide on any event except close button*/
        });
    });
}

reloadQtip();

// Función que actualiza el menú dinámicamente
function updateMenu() {
    $.ajax({
        type: "GET",
        url: 'ajax.php',
        data: {op: 'updateMenu'},
        success: function(data) {
            $('#menucontainer').html(data);
            updateMenuCss();
        }
    });
}

function openUrlInWindow(url, width, height) {
    var defaultWidth = 800;
    var defaultHeight = 600;

    console.log(width);
    console.log(height);

    if (typeof  width !== "undefined") {
        defaultWidth = width;
    }

    if (typeof  height !== "undefined") {
        defaultHeight = height;
    }

    window.open(url, "", "toolbar=yes,scrollbars=yes,resizable=yes,width=" + defaultWidth + ",height=" + defaultHeight);

}

function updateMenuCss() {
    $("#cssmenu").menumaker({
        title: "Menu",
        format: "multitoggle"
    });
}

var regionalDatePicker = {
    closeText: 'Cerrar',
    prevText: '<Ant',
    nextText: 'Sig>',
    currentText: 'Hoy',
    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
    dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
    weekHeader: 'Sm',
    dateFormat: 'dd/mm/yy',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''
};

var counter;

function loadOrdersWidget() {
    setTimeout(function() {
        $.ajax({
            url: 'ajax.php',
            type: 'post',
            data: {
                op: 'loadOrdersWidget',

            }
        }).done(function(Response) {
            //alert(Response);
            var res = JSON.parse(Response);
            $('#widget-cart').html(res['news']);
            $('#widget-incompletes').html(res['incompletes']);
        });
        loadOrdersWidget();
    }, 60000); // 300.000 ms = 5 minutos, 60000 ms = 1 minuto
}

function addCommas(id, n) {
    n = n.toString();
    commas = n.split(".");
    ncommas = commas.length;

    for(i = 0; i < ncommas; i++) {
        n = n.replace(".", "");
    }

    while (true) {
        var n2 = n.replace(/(\d)(\d{3})($|,|\.)/g, '$1.$2$3');
        if (n == n2) break
        n = n2
    }

    $("#" + id).prop("value", n);
}

if (userRepostitory) {
    loadOrdersWidget();
}
