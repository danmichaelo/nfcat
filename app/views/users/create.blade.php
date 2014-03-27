@extends('layouts.master')

@section('content')

    <h1>{{ _('Welcome!') }}</h1>

    <div id="carousel">
    <ul>

        <li id="part0">
            <div class="container">

                <img src="/yvonne/spinner.gif" style="display:block;margin-left:auto;margin-right:auto; width:128px; height:128px;"/>

                <div class="errors"></div>

            </div> 
        </li>

        <li id="part1">
            <div class="container">

                {{ Form::model(new User(), array(
                  'action' => array('UsersController@postStore'),
                  'method' => 'post',
                  'class' => 'simpleform'
                )) }}

                    
                      {{ Form::label('ltid', _('Library card number (10 characters):')) }}
                      {{ Form::text('ltid', null, array(
                        'autocomplete' => 'off',
                        'class' => 'input-big'
                        )) }}
                    

                  <button type="submit" class="btn">{{ _('Continue') }}</button>

                {{ Form::close() }}
                <p class="errors"></p>

                <p class="center">
                    {{ _('Language') }}:<br /> 
                    <a href="/set-locale/nb_NO" style="inline-block;padding:8px;">Norsk bokmål</a>
                    •
                    <a href="/set-locale/en_US" style="inline-block;padding:8px;">English</a>
                </p>

            </div> 
        </li>

        <li>
            <p class="center">
                {{ _('Browsing the catalogue...') }}
            </p>
            
            <img src="/yvonne/spinner.gif" style="display:block;margin-left:auto;margin-right:auto; width:128px; height:128px;"/>

        </li>

        <li id="part2">
            <div class="container">
                <div class="errors"></div>
                <form method="POST" action="http://nfcat.biblionaut.net/users/activate" accept-charset="UTF-8" class="simpleform">

                  <input type="number" autocomplete="off" id="activation_code" name="activation_code" class="input-big" max="9999" />

                  <button type="submit" class="btn">{{ _('Continue') }}</button>

                  <p>
                    {{ _('Not receiving SMS verification code?') }}
                  </p>
                <ul>
                    <li>{{ _('Please allow some time for network delays') }}</li>
                    <li>{{ _('Switched phone no. since you enrolled university? Check with a librarian if we have your correct number.') }}</li>
                </ul>

                </form>
            </div> 
        </li>

        <li>
            
            <p class="center">
                {{ _('Hold on... Library cat is working...') }}
            </p>
            <img src="/yvonne/spinner.gif" style="display:block;margin-left:auto;margin-right:auto; width:128px; height:128px;"/>

        </li>

        <li id="part3">
            <div class="container">
                <div class="errors"></div>

                <p class="center">
                    {{ _('Final step – we promise!') }}
                </p>

                <form method="POST" action="http://nfcat.biblionaut.net/users/activate" accept-charset="UTF-8" class="simpleform">

                  <label for="own_pin">{{ _('Please choose <em>your own</em> four digit pin code. You will be asked for this when checking out books using your mobile.') }}</label> 
                  <input id="own_pin" name="own_pin" type="number" class="input-big" max="9999" />

                  <button type="submit" class="btn">{{ _('Complete!') }}</button>

                </form>
            </div> 
        </li>

        <li>
            
            <p class="center">
                {{ _('Hold on... Library cat is working...') }}
            </p>
            <img src="/yvonne/spinner.gif" style="display:block;margin-left:auto;margin-right:auto; width:128px; height:128px;"/>

        </li>

        <li id="lastpart">
            <p class="center">
                {{ _('Congrats! You are now ready to use your phone to checkout books!') }}
            </p>
            
            <img src="/yvonne/MemeFace/happyMeme7.png" style="display:block;margin-left:auto;margin-right:auto; width:60%;"/>

        </li>        

    </ul>
    </div>

    

@stop

@section('scripts')

<script type='text/javascript'>


function Carousel(element) {

    element = $(element);

    var self = this;

    var container = $(">ul", element);
    var panes = $(">ul>li", element);

    var pane_width = 0;
    var pane_count = panes.length;
    var current_pane = 0;
    console.info("Found " + pane_count + " panes");

    /**
     * set the pane dimensions and scale the container
     */
    function setPaneDimensions() {
        //console.info("Update pane dim.");
        pane_width = element.width();
        panes.each(function() {
            $(this).width(pane_width);
        });
        container.width(pane_width*pane_count);
    };


    /**
     * show pane by index
     * @param   {Number}    index
     */
    this.showPane = function( index ) {
        // between the bounds
        index = Math.max(0, Math.min(index, pane_count-1));
        current_pane = index;

        var offset = -((100/pane_count)*current_pane);
        setContainerOffset(offset, true);
    };


    function setContainerOffset(percent, animate) {
        container.removeClass('animate');

        if(animate) {
            container.addClass('animate');
        }

        container.css('transform', 'translate3d(' + percent + '%,0,0) scale3d(1,1,1)');

    }
    /**
     * Init
     */
    this.init = function() {

        setPaneDimensions();
        $(window).on("load resize orientationchange", function() {
            setPaneDimensions();
            //updateOffset();
        });

    }
}

var carousel = new Carousel("#carousel");

var messages = {
    'invalid-ltid-format': "{{ _('The card number is not of correct length') }}",
    'ltid-unregistered': "{{ _('The card number was not found in BIBSYS. If you entered it correctly, please contact a librarian for assistance.') }}",
    'no-phone': "{{ _('There is no cell phone number connected with your library card number. pPlease contact a librarian for assistance.') }}",
    'unknown-phone-format': "{{ _('The format of your registered phone number could not be recognized. Please contact a librarian for assistance.') }}",
    'too-rapid': "{{ _('We did not send you a new activation code since we sent one less than 30 minutes ago. The previous activation code is still valid.') }}",
    'code-sent': "{{ _('We have sent you an SMS verification code valid for 30 minutes. Please enter it below:') }}",
    'server-error': "{{ _('Oh no, our server is not well right now... Please try again in a short time.') }}",
    'network-problem': "{{ _('Oh no, we have network problems.') }}",
    'code-expired': "{{ _('Ops, the code has expired. To request a new one, just press continue.') }}",
    'code-invalid': "{{ _('Ops, seems like the code you entered was not valid.') }}",
    'too-many-attempts': "{{ _('Sorry, to protect your security we only allow three attemps. You can request a new activation code once the current has expired.') }}",
    'pin-invalid': "{{ _('Sorry, the PIN code must consist of exactly four digits.') }}",
    'pin-too-simple': "{{ _('Library cat finds your PIN cute, but is a bit concerned about your security. Please try a slightly more secure PIN.') }}",
    'pin-already-set': "{{ _('A PIN code has already been created for this device.') }}",
}

$(document).ready(function() {
    var error_img = '<img src="/yvonne/annoyed-facepalm-picard-l.png" style="margin-top:16px; width:70%;display:block;margin-left:auto;margin-right:auto;" /> ';
    var devicekey = '',
        ltid = '';
    //$('#carousel li').first().find('img').show().css('display', 'block');
    carousel.init();
    $('body > .container').show();

    setTimeout(function() {
        
        $('#part0 .errors').html('Kontakter telefonen...');
        if (window.JSInterface !== undefined) {
            $('#part0 .errors').html('Grensesnittet eksisterer');

            if (window.JSInterface.helloDroid() == "ehlo") {
                $('#part0 .errors').html('Fikk kontakt med telefonen');
                carousel.showPane(1);
                //window.JSInterface.storeNewDeviceKey('ubo0223', 'Kake');
            }
        } else {
            $('#part0 .errors').html('Ingen kontakt med telefonen');
        }

    }, 1000);
    
    $('button').on('touchstart', function(e) {
        $(this).addClass("active");
    });
    $(document).on('touchend touchcancel', function(e) {
        $('button').removeClass("active");
    });

    $('#part1 form').on('submit', function(evt) {
        $('.errors').html('');
        $('#ltid').blur();
        ltid = $('#ltid').val();
        carousel.showPane(2);
        $.post('/users/store', { ltid: ltid })
        .done(function(response) {
            if (response.error) {
                $('#part1 .errors').html(messages[response.error] || response.error);
                carousel.showPane(1);
            } else {
                if (response.notice) {
                    $('#part2 .errors').html(messages[response.notice]);
                } else {
                    $('#part2 .errors').html(messages['code-sent']);
                }
                carousel.showPane(3);
            }
        })
        .error(function(e,e2) {
            if (e.status && e.status == 500) {
                $('#part1 .errors').html(error_img + messages['server-error'] );
            } else {
                $('#part1 .errors').html(error_img + messages['network-problem'] );
            }
            carousel.showPane(1);
        });
        return false;
    });


    $('#part2 form').on('submit', function(evt) {
        $('.errors').html('');
        $('#activation_code').blur();
        carousel.showPane(4);
        $.post('/activation-codes/verify', { ltid: $('#ltid').val(), activation_code: $('#activation_code').val() })
        .done(function(response) {
            $('#activation_code').val('');
            if (response.error) {
                if (response.error == 'code-expired' || response.error == 'too-many-attempts') {
                    $('#part1 .errors').html(messages[response.error] || response.error);
                    carousel.showPane(1);                    
                } else {
                    $('#part2 .errors').html(messages[response.error] || response.error);
                    carousel.showPane(3);                    
                }
            } else {
                devicekey = response.key;
                window.JSInterface.storeNewDeviceKey(ltid, devicekey);
                carousel.showPane(5);
            }
        })
        .error(function(e,e2) {
            if (e.status && e.status == 500) {
                $('#part2 .errors').html(error_img + messages['server-error'] );
            } else {
                $('#part2 .errors').html(error_img + messages['network-problem'] );
            }
            carousel.showPane(3);
        });
        return false;
    });


    $('#part3 form').on('submit', function(evt) {
        $('.errors').html('');
        $('#own_pin').blur();
        carousel.showPane(6);
        $.post('/device-keys/store-pin', { ltid: $('#ltid').val(), key: devicekey, pin: $('#own_pin').val() })
        .done(function(response) {
            $('#own_pin').val('');
            if (response.error) {
                $('#part3 .errors').html(messages[response.error] || response.error);
                carousel.showPane(5);
            } else {
                carousel.showPane(7);
            }
        })
        .error(function(e,e2) {
            if (e.status && e.status == 500) {
                $('#part3 .errors').html(error_img + messages['server-error'] );
            } else {
                $('#part3 .errors').html(error_img + messages['network-problem'] );
            }
            carousel.showPane(5);
        });
        return false;
    });

    $('#lastpart').on('click', function(e) {
        window.JSInterface.closeWizard();
    });

});

</script>


@stop