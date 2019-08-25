var hostname = window.location.hostname;
var protocol = window.location.protocol;
var slashes = protocol.concat('//');
var devPath = (hostname == '127.0.0.1' || hostname == 'localhost') ? 'nova/' : '';
var host = slashes.concat(hostname) + '/';
host = host.concat(devPath);

var isEngMode = false;
var messageKey = 'th';

var errorMessage = {
    'default': {
        'th': 'กรุณากรอกข้อมูลให้ถูกต้อง',
        'eng': 'Please validate your input.'
    }
    , 'rq': {
        'th': 'กรุณากรอกข้อมูลให้ครบถ้วน',
        'eng': 'This is required field.'
    }
    , 'iden': {
        'th': 'เลขประจำตัวประชาชนไม่ถูกต้อง',
        'eng': 'Invalid format, please check.'
    }
    , 'mobl_fm': {
        'th': 'รูปแบบหมายเลขโทรศัพท์ไม่ถูกต้อง',
        'eng': 'Invalid format, please check.'
    }
    , 'mail_fm': {
        'th': 'รูปแบบ Email ไม่ถูกต้อง',
        'eng': 'Invalid format, please check.'
    }
    , 'accepted': {
        'th': 'กรุณายอมรับเงื่อนไขการร่วมกิจกรรม',
        'eng': 'Please check to accept before submit.'
    }    
};

$(document).ready(function() {
    fixHostBug ();
    
    $("#registerBtn").on( "click", register);
    $("#clearBtn").on( "click", onClearClicked);

    $("#firstname").on( "blur", requireField.bind(this, 'firstname') );
    $("#lastname").on( "blur", requireField.bind(this, 'lastname') );

    $("#identifier").on( "blur", validateIdentifier.bind(this, 'identifier') );

    $("#email").on( "blur", validateEmail.bind(this, 'email') );
    $("#mobile").on( "blur", validateMobile.bind(this, 'mobile') );

    $("#accepted").change(function() {
        requireAcknowledge('accepted');
    });

    isEngMode = $("#mode").val() == 'english';
    messageKey = isEngMode ? 'eng' : 'th'; 
});

function fixHostBug () {
   /* var bugurl = window.location.host + window.location.pathname;
    var target = 'campaign.consumer.huawei.com/th/registermate9/web/views/register.html';
    if(bugurl == target) {
        window.location = 'http://campaign.consumer.huawei.com/th/registermate9';
    }*/
}


/**
**  ========== Action =============
**/
function onClearClicked ( event ) {
    $('#firstname').val('');
    $('#lastname').val('');
    $('#email').val('');
    $('#mobile').val('');
    $('#identifier').val('');
    $('#address').val('');
    $('#color').val(1);
    $('#accepted').prop('checked', false);

    event.preventDefault();
    event.stopPropagation();
};


function register ( event ) {
    var properties = {};
    
    properties['firstname'] = requireField('firstname');
    properties['lastname'] = requireField('lastname');
    properties['email'] = validateEmail('email');
    properties['mobile'] = validateMobile('mobile');
    properties['identifier'] = validateIdentifier('identifier');
    properties['accepted'] = requireAcknowledge('accepted');

    for (var prop in properties) {
        if(!properties.hasOwnProperty(prop)) continue;
        if(properties[prop] == false) {
            event.preventDefault();
            return false;
        }
    }

    var payload = {
        'data': {
            'sex': $("#sex").val()
            , 'firstname': $("#firstname").val()
            , 'lastname': $("#lastname").val()
            , 'mobile': $("#mobile").val()
            , 'email': $("#email").val()
            , 'identifier': $("#identifier").val()
            , 'address': $("#address").val()
            , 'color': $('input[name=color]:checked').val()
            , 'accepted': $("#accepted").val()     
        }
        , 'action': 'register'        
    };

    var registerBtn = $("#registerBtn");
    registerBtn.prop('disabled', true);
    H5_loading.show();

    var servicesUrl = host + 'services/register.php';
    try {
        $.ajax({
            url: servicesUrl
            , type: "POST"
            , data: JSON.stringify(payload)
            , success: function ( response ) {
                try {
                    var resp = JSON.parse((response || '' ));

                    if( resp['status'] == 'success' ) {

                        window.location.href = './thankyou.html?id=' + resp['identifier'] + 'cid=' + resp['customerid'];

                    } else {
                        // TODOL: Add error message to inform user why cannot register
                    }

                } catch (ex) {
                    console.log(ex.stack);

                } finally {
                    registerBtn.prop('disabled', false);
                    H5_loading.hide();
                }
            }
            , error: function() {
                registerBtn.prop('disabled', false);
                H5_loading.hide();
            }
            , complete: function() {}
        });
    }
    catch (err) {
        console.log(err.stack);
    }

    event.preventDefault();
    return false;
};

/**
**  ========== QR Code Generator =============
**/
function getQrCode () {

}


/**
**  ========== Validation =============
**/

function validateIdentifier(id) {
    var targetDiv = $("#" + id);
    if(targetDiv.length == 0) {
        return true;
    }
    var container = targetDiv.closest("div");
    var glypIcon = $("#glypcn" + id);

    var x = new String(targetDiv.val());
    splitext = x.split('');
    var total = 0;
    var mul = 13;

    for(i = 0; i < splitext.length-1; i++) {
        total = total + splitext[i] * mul;
        mul = mul -1;
    }
    
    var mod = total % 11;
    var nsub = 11 - mod;
    var mod2 = nsub % 10;
    
    var isValid = mod2 == splitext[12];

    if(!isValid) {
        return onInValidControl( id, container, glypIcon, 'iden' );
    } else {
        return onValidControl( id, container, glypIcon );
    }
}

function requireField( id ) {
    var targetDiv = $("#" + id);
    if(targetDiv.length == 0) {
        return true;
    }
    var container = targetDiv.closest("div");
    var glypIcon = $("#glypcn" + id);

    if(targetDiv.val() == null || targetDiv.val() == "") {
        return onInValidControl( id, container, glypIcon, 'rq');
    } else {
        return onValidControl( id, container, glypIcon );
    }
}

function requireAcknowledge( id ) {
    var targetChkb = $("#" + id);
    if(targetChkb.length == 0) {
        return true;
    }
    var container = targetChkb.closest("div");
    var errorMessageEle = container.find('small.custom-error');
    if (errorMessageEle.length > 0) {
        errorMessageEle.remove()
    }

    if(targetChkb.prop('checked')) {
        return true;
    } else {
        container.append('<small class="custom-error">' + errorMessage['accepted'][messageKey] + '</small>');
        return false;
    }
}

function validateEmail( id ) {
    var email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
    var targetDiv = $("#" + id);
    var container = targetDiv.closest("div");
    var glypIcon = $("#glypcn" + id);

    if(!email_regex.test(targetDiv.val())) {
        return onInValidControl( id, container, glypIcon, 'mail_fm' );
    } else {
        return onValidControl( id, container, glypIcon );
    }
}

function validateMobile( id ) {
    var mobile_regex = /^0[0-9]{9}$/i;
    var targetDiv = $("#" + id);
    var container = targetDiv.closest("div");
    var glypIcon = $("#glypcn" + id);

    if(!mobile_regex.test(targetDiv.val())) {
        return onInValidControl( id, container, glypIcon, 'mobl_fm' );
    } else {
        return onValidControl( id, container, glypIcon );
    }
}

function onValidControl( id, container, glypIcon ) {
    container.removeClass("has-error");
    glypIcon.remove();
    container.addClass("has-success has-feedback");
    container.append('<span id="glypcn' + id + '" class="glyphicon glyphicon-ok form-control-feedback"></span>');
    container.find('.custom-error').remove();
    return true;
}

function onInValidControl( id, container, glypIcon, key ) {
    container.removeClass("has-success");
    container.find('.custom-error').remove();
    glypIcon.remove();
    container.addClass("has-error has-feedback");
    container.append('<span id="glypcn' + id + '" class="glyphicon glyphicon-remove form-control-feedback"></span>');
    container.append('<small class="custom-error">' + errorMessage[key][messageKey] + '</small>');
    return false;
}
