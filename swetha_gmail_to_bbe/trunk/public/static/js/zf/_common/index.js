/**
 * @param title
 * @param message
 * @param type: warning | danger | success | info
 */
function bs_notification(title, message, type)
{
	var id= 'bs_notification_'+$(".bs_notification").length;
	$("#notification_wrap").append('<div id="'+id+'" class="bs_notification alert alert-' + type + '">'
	+ '<a href="#" class="close" data-dismiss="alert">&times; (<span id="' + id + '_time">5</span>s)</a>'
	+ '<strong>' + title + '</strong> ' + message
	+ '</div>');
	
	var interval = setInterval(function() {
		$("#" + id + "_time").html($("#" + id + "_time").html() - 1);
	}, 1000);
	window.setTimeout(function() { 
		$("#" + id).alert('close');
		clearInterval(interval);
	}, 5000);
}

function bbe_bugs_show_logout_menu()
{
	bootbox.dialog( {
		message :
			'<button class="btn btn-primary" style="width:100%;" onclick="window.open(location,\'_blank\'); ">'
				+ '<span class="glyphicon glyphicon-plus"></span> Open instance in new page'
			+ '</button>'
			+ '<button class="btn btn-danger" style="width:100%;" onclick="bbe_bugs_logout_action();">'
				+ '<span class="glyphicon glyphicon-log-out"></span> Logout'
			+ '</button>'
		,
		title : "Select an action",
		buttons : {
			success : {
				label : "Cancel",
				className : "btn",
				callback : function() {
					
				}
			}
		}
	});	
}

function bbe_bugs_logout_action()
{
	$.ajax({
		type		: 'POST',
		url			: BASE_URL + "auth/auth/logout/format/json",
		data		: {
			
		},
		dataType	: 'json',
		success		: function(json)
		{
			location.reload();
		},
		error		: function( objRequest )
		{
			bs_notification("Error:", "Something went wrong!", "danger");
		}
	});
}
