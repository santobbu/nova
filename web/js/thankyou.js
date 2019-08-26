var hostname = window.location.hostname;
var protocol = window.location.protocol;
var slashes = protocol.concat('//');
var devPath = (hostname == '127.0.0.1' || hostname == 'localhost') ? 'nova/' : '';
var host = slashes.concat(hostname) + '/';
host = host.concat(devPath);

$(document).ready(function() {
    var customerId = qs('id');
    
    var request = {
      "data": customerId,
      "config":{
        "body":"square",
        },
      "download": true,
      "file": "png"
       , 'action': 'test' 
    };

    var settings = {
      "async": true,
      "crossDomain": true,
      "url": host + 'services/register.php', 
      // "url": "https://qrcode-monkey.p.rapidapi.com/qr/custom",
      "method": "POST",
      "data": JSON.stringify(request),
      "headers": {
        "x-rapidapi-host": "qrcode-monkey.p.rapidapi.com",
        "x-rapidapi-key": "ddf3376362msh0348c3a4c4e5334p106f9djsn2b51e4dceb66",
        "content-type": "application/json",
        "accept": "application/json"
      }
    }

    $.ajax(settings)
    .done(function (response) {
      var resp = response;
      try {
        var resp = response && JSON.parse((response || '' ));
      } catch(e) {
        resp = response;
      }
    // show response QR code
      if (resp && resp.imageUrl) {
        var loading = $('#loading-spinner');
        var qrImage = $('#qr-image');

        loading.hide();
        qrImage.attr("src", resp.imageUrl);
        qrImage.show();

        // send email
        sendEmail(resp.imageUrl);

      } else {
        displayWarningMessage();
      }
    });

  }); // end $(document).ready

  function displayWarningMessage () {
    $('#loading-spinner').hide();
    $('#qr-image').hide();
    $('#error-message').show();
  }

  function gotoMain () {
    window.location.href = "http://huaweifestival.com";
  }

  function qs(key) {
      key = key.replace(/[*+?^$.\[\]{}()|\\\/]/g, "\\$&"); // escape RegEx meta chars
      var match = location.search.match(new RegExp("[?&]" + key + "=([^&]+)(&|$)"));
      return match && decodeURIComponent(match[1].replace(/\+/g, " "));
  }

  function sendEmail (qrUrl) {
    var payload = {
      'data': {
        'customerid': qs('cid')
        , 'qrurl': qrUrl
      }
      , 'action': 'thankyou'        
    };

  try {
      $.ajax({
          url: host + 'services/register.php'
          , type: "POST"
          , data: JSON.stringify(payload)
          , success: function ( response ) {
              try {
                console.log(response);

              } catch (ex) {
                  console.log(ex.stack);
              }
          }
          , error: function() {}
          , complete: function() {}
      });
  }
  catch (err) {
      console.log(err.stack);
  }
}
