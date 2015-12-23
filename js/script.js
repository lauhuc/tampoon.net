/*you can use these globals variables (they are wrtitten by PHP in ../index.php and passed to JS)

var tampoonFirstRate
var tampoonSecondRate
var minimumQuantityOrder
var currency
var locale
 */

var sum;
var total;
var styleTampoonContainer = 'border: 2px solid cornflowerblue;border-radius: 3px;padding: 5px;';

function makeSum()
{
    sum = 0;

    var inputs = document.querySelectorAll('#main input');

    for(var i = 0; i < inputs.length; i++)
    {
        if(inputs[i].value != '')
        {

            sum += parseInt(inputs[i].value);
        }
    }

    if(isNaN(sum)){

        sum = 0;
        alert(translations[locale][0]);

    }
    var divInfo = document.getElementById('return_from_makeSum');

    divInfo.innerHTML = translations[locale][9]+': '+sum+'<br>';

    if(sum < 100)
    {
        total = sum * tampoonFirstRate;
        divInfo.innerHTML += 'Total: '+total+' '+currency+'<br>';

    }else
    {
        total = sum * tampoonSecondRate;
        divInfo.innerHTML += 'Total: '+total+' '+currency+'<br>'; }

    if(sum >= minimumQuantityOrder)
    {
        divInfo.innerHTML += '<p><a id="for_send" href="#" onclick="checkValues();">'+translations[locale][1]+'</a></p>';
    }

    if(sum > 0)
    {
        document.getElementById('infos').style.display = 'block';

    }else{
        document.getElementById('infos').style.display = 'none';
    }

}

function checkValues()
{
    document.getElementById('checkvalues').style.visibility = 'visible';

    var htmOutput = '<input type="email" class="bigInput" id="clientEmail" name="clientEmail" placeholder="'+translations[locale][2]+'" autofocus /><br><br>';
    htmOutput += '<input type="password" class="bigInput" placeholder="'+translations[locale][3]+'" id="password"/> <p id="return_from_processOrder"></p>';
    htmOutput += '<p id="pForProcessOrder"><a href="#" style="font-size: 20px;"  onclick="processOrder();">'+translations[locale][4]+'</a></p>';

    document.getElementById('return_from_checkvalues').innerHTML = htmOutput;
}

function processOrder()
{
    var email = document.getElementById('clientEmail').value;

    if(email != '')
    {
        var standingUnits = document.getElementsByName('standing_unit');
        var containerSendMailLink = document.getElementById('pForProcessOrder');
        containerSendMailLink.innerHTML = '<img src="../img/spinner.gif" style="border: none;" />';

        var oData = new FormData(document.forms.namedItem('the_form'));
        var oReq = new XMLHttpRequest();

        oReq.open('POST', '../ajax/processOrder.php', true);

        oData.append('clientEmail', email);
        oData.append('password', document.getElementById('password').value);
        oData.append('quantityTampoon', sum);
        oData.append('total', total);

        for(var i = 0; i < standingUnits.length; i++)
        {
            if(standingUnits[i].checked)
            {
                oData.append('standingUnit', standingUnits[i].value);
            }
        }

        oReq.onload = function(oEvent)
        {
            if (oReq.status === 200)
            {
                if(oReq.responseText.substr(0,1) === 'e')
                {
                    containerSendMailLink.innerHTML = '<a href="#" style="font-size: 16px;"  onclick="processOrder();">'+translations[locale][4]+'</a>';
                    document.getElementById('return_from_processOrder').innerHTML = oReq.responseText.substr(1);

                }else
                {
                    containerSendMailLink.innerHTML = '';
                    document.getElementById('return_from_processOrder').innerHTML = oReq.responseText;
                }

            } else {
                document.getElementById('return_from_processOrder').innerHTML = 'Error ' + oReq.status;
            }
        };

        oReq.send(oData);

    }else{ alert(translations[locale][5]); }
}

function clearAllInputsValues()
{
    var inputs = document.querySelectorAll('#main input');

    for(var i = 0; i < inputs.length; i++)
    {
        inputs[i].value = '';
        document.getElementById('container_'+inputs[i].id).style.cssText = 'border: none;';
    }

    makeSum();
}

function fillAllWith1Q()
{
    var inputs = document.querySelectorAll('#main input');

    for(var i = 0; i < inputs.length; i++)
    {
        document.getElementById('container_'+inputs[i].name).style.cssText = 'border: none;';
        inputs[i].value = 1;
    }

    makeSum();
}

function fill50ValWithXQ(p1_which_quantity)
{
    clearAllInputsValues();

    var inputs = document.querySelectorAll('#main input');

    var inputs_name = [];

    for(var i = 0; i < inputs.length; i++)
    {
        document.getElementById('container_'+inputs[i].name).style.cssText = 'border: none;';
        inputs_name.push(inputs[i].name);
    }

    var array_random = array_rand(inputs_name, 50);

    for(var prop in array_random)
    {
        document.getElementById(inputs_name[array_random[prop]]).value = p1_which_quantity;
        document.getElementById('container_'+inputs_name[array_random[prop]]).style.cssText = styleTampoonContainer;

    }

    makeSum();
}

function fillXQuantitiesWithXItems(p1_which_quantity, p2_differents_items)
{
    if(p1_which_quantity != '' && p2_differents_items != '')
    {
        var p1_q = parseInt(p1_which_quantity);
        var p2_diff_items = parseInt(p2_differents_items);

        if(p1_q != 0 && p2_diff_items != 0)
        {
            if(p2_diff_items > 1)
            {
                clearAllInputsValues();

                var inputs = document.querySelectorAll('#main input');

                var numInputs = inputs.length;

                if(p2_diff_items <= numInputs)
                {
                    var availableTampoons = [];

                    for(var i = 0; i < inputs.length; i++)
                    {
                        document.getElementById('container_'+inputs[i].name).style.cssText = 'border: none;';

                        if(inputs[i].max >= p1_q)
                        {
                            //console.log(inputs[i].id+' : '+inputs[i].max);
                            availableTampoons.push(inputs[i].name);
                        }
                    }
                    //console.log(availableTampoons.length+' >= '+p2_diff_items);

                    if(availableTampoons.length >= p2_diff_items)
                    {
                        var array_random = array_rand(availableTampoons, p2_diff_items);

                        for(var prop in array_random)
                        {
                            document.getElementById(availableTampoons[array_random[prop]]).value = p1_q;
                            document.getElementById('container_'+availableTampoons[array_random[prop]]).style.cssText = styleTampoonContainer;
                        }

                        makeSum();

                    }else{ alert(translations[locale][6]); }

                }else{ alert(translations[locale][7]+numInputs); }

            }else{ alert(translations[locale][8]); }
        }
    }
}

function changePassword()
{
    var oOutput = document.getElementById('return_from_changePassword');

    document.getElementById('containerLinkAction').innerHTML = '<img src="../img/spinner.gif" style="border: none;" />';

    var oData = new FormData(document.forms.namedItem('the_form'));

    var oReq = new XMLHttpRequest();

    oReq.open('POST', '../ajax/changePassword.php', true);

    oReq.onload = function(oEvent)
    {
        if (oReq.status === 200)
        {
            if(oReq.responseText.substr(0, 1) === 'e')
            {
                document.getElementById('containerLinkAction').innerHTML = '<a href="#" onclick="changePassword();">Change</a><br>';
                oOutput.innerHTML = oReq.responseText.substr(1);

            }else
            {
                document.getElementById('containerLinkAction').innerHTML = '';
                oOutput.innerHTML = oReq.responseText;

            }

        }else
        {
            oOutput.innerHTML = 'Error ' + oReq.status;
        }
    };

    oReq.send(oData);
}

function switchDivDisplay(p1_tampoon_q, p2_div_container_id)
{
    if(p1_tampoon_q == 0)
    {
        document.getElementById(p2_div_container_id).style.cssText = 'border: none;';

    }else{
        document.getElementById(p2_div_container_id).style.cssText = styleTampoonContainer;
    }
}

function handleSession(p1_email)
{
    if(p1_email.length > 5) //an email contains at least: x@x.xx
    {
        document.getElementById('return_from_handleSession').innerHTML = '<img src="../img/spinner.gif" style="border: none;" />';

        var oData = new FormData(document.forms.namedItem('the_form'));

        var oReq = new XMLHttpRequest();

        oReq.open('POST', '../ajax/handleSession.php', true);

        oReq.onload = function(oEvent)
        {
            if (oReq.status === 200)
            {
                if(oReq.responseText.substr(0, 1) === 'e')
                {
                    document.getElementById('return_from_handleSession').innerHTML = oReq.responseText.substr(1);

                }else
                {
                    document.getElementById('return_from_handleSession').innerHTML = oReq.responseText;
                }

            }else
            {
                document.getElementById('return_from_handleSession').innerHTML = 'Error ' + oReq.status;
            }
        };

        oReq.send(oData);
    }
}

function array_rand(input, num_req) {
    //  discuss at: http://phpjs.org/functions/array_rand/
    // original by: Waldo Malqui Silva (http://waldo.malqui.info)
    //   example 1: array_rand( ['Kevin'], 1 );
    //   returns 1: 0

    var indexes = [];
    var ticks = num_req || 1;
    var checkDuplicate = function (input, value) {
        var exist = false,
            index = 0,
            il = input.length;
        while (index < il) {
            if (input[index] === value) {
                exist = true;
                break;
            }
            index++;
        }
        return exist;
    };

    if (Object.prototype.toString.call(input) === '[object Array]' && ticks <= input.length) {
        while (true) {
            var rand = Math.floor((Math.random() * input.length));
            if (indexes.length === ticks) {
                break;
            }
            if (!checkDuplicate(indexes, rand)) {
                indexes.push(rand);
            }
        }
    } else {
        indexes = null;
    }

    return ((ticks == 1) ? indexes.join() : indexes);
}