var hostname = window.location.hostname;
var protocol = window.location.protocol;
var slashes = protocol.concat('//');
var devPath = (hostname == '127.0.0.1' || hostname == 'localhost') ? 'nova/' : '';
var host = slashes.concat(hostname) + '/';
host = host.concat(devPath);

$(document).ready(function() {
    fixHostBug ();
    
    var today = new Date().toISOString().split('T')[0] + ' 09:00';
    $("#submitBtn").on( "click", submit);
});

function fixHostBug () {
    /*var bugurl = window.location.host + window.location.pathname;
    var target = 'campaign.consumer.huawei.com/th/registermate9/web/views/register.html';
    if(bugurl == target) {
        window.location = 'http://campaign.consumer.huawei.com/th/registermate9';
    }*/
}


/**
**  ========== Action =============
**/

function submit ( event ) {
    var startdateVal = $("#startdate").val();
    var enddateVal = $("#enddate").val();
    var payload = {
        'startdate': startdateVal.replace('T', ' ')
        , 'enddate': enddateVal.replace('T', ' ')   
        , 'action': 'report'        
    };

    var submitBtn = $("#submitBtn");
    submitBtn.prop('disabled', true);
    H5_loading.show();

    try {
        $.ajax({
            url: host + 'services/register.php'
            , type: "post"
            , data: JSON.stringify(payload)
            , success: function ( response ) {
                try {
                    var resp = JSON.parse((response || '' ));

                    if( resp['status'] == 'success' ) {
                        var table = $('#result');
                        var bodyTable = table.find('tbody');

                        // clear all row
                        bodyTable.find('tr').remove();

                        var rows = resp['data'] || [];
                        for (var i = 0; i < rows.length; i++) {

                            var item = rows[i];
                            var dateItem = item['createddate'];
                            var dateArr = dateItem && dateItem.split(' ');
                            var dateString = '', timeString = '';
                            if (dateArr && dateArr.length >= 2) {
                                var dateObj = new Date(dateArr[0]);
                                dateString = dateObj.toLocaleString().split(' ')[0];
                                timeString = dateArr[1] && dateArr[1].length > 6 ? dateArr[1].slice(0, 5) : '';
                            }

                            var row = $('<tr>');
                            row.append($('<td>' +  (i + 1) + '</td>'));
                            row.append($('<td>' + item['identifier'] + '</td>'));
                            row.append($('<td>' + item['prefix'] + '</td>'));
                            row.append($('<td>' + item['firstname'] + '</td>'));
                            row.append($('<td>' + item['lastname'] + '</td>'));
                            row.append($('<td>' + item['email'] + '</td>'));
                            row.append($('<td>' + item['mobile'] + '</td>'));
                            row.append($('<td>' + item['colorname'] + '</td>'));
                            row.append($('<td><a target="_blank" href="' + item['qrurl'] + '">Click</a></td>'));
                            row.append($('<td>' + dateString + '</td>'));
                            row.append($('<td>' + timeString + '</td>'));
                            bodyTable.append(row);
                        }

                    } else {
                        alert('ไม่สามารถดึงข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
                    }

                } catch (ex) {
                    console.log(ex.stack);
                } finally {
                    submitBtn.prop('disabled', false);
                    H5_loading.hide();
                }
            }
            , error: function() {
                submitBtn.prop('disabled', false);
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