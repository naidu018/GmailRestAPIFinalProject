var oTable;
var dTable;
var checkedEmails = [];


$(document).ready(function() {
    oTable = $('#show_emails_table').dataTable({
        "iDisplayLength": 100,
		"bProcessing": true,
		"bServerSide": true,
//		"sAutoWidth": true,
//		"bAutoWidth": true,
		"sAjaxSource": BASE_URL + "email/mail-ajax/get-data-table/format/json/is_deleted/0",
		"bJQueryUI": false,
		"sPaginationType": "full_numbers",
		
		"drawCallback": function(){
	    	$('.has-poshytip').poshytip({
				className: 'tip-twitter',
				showTimeout: 1,
				alignTo: 'target',
				alignX: 'center',
				offsetY: 5,
				allowTipHover: false,
				fade: false,
				slide: false
			});
	    	
	    	$.each(checkedEmails, function(){
    			$("#bbe_bugs_email_multicheck_" + this).prop("checked", true);
    		});
	    	
	    	bbe_bugs_count_msg_to_delete();
	    	
	    	$("#show_emails_table tr.is_not_read:last td").addClass("last-email-not-read");
	    	
    	},
    	
    	"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			
    		$.each(aData, function(k, v){
    			var extra_classes = $(v).attr('extra_classes');
    			
    			if (typeof extra_classes !== typeof undefined && extra_classes !== false)
    			{
    				$(nRow).addClass(extra_classes);
    			}
    		});
    		
		},
		
		"aoColumns": [
						{ "bSortable": true, "sWidth": "5%" },
						{ "bSortable": true, "sWidth": "15%" },
						{ "bSortable": true, "sWidth": "45%" },
						{ "bSortable": false, "sWidth": "1%" },
						{ "bSortable": true, "sWidth": "20%" },
						{ "bSortable": true, "sWidth": "10%" },
						{ "bSortable": true, "sWidth": "3%" },
						{ "bSortable": true, "sWidth": "1%" }
		],
		"order": [[ 6, "asc" ]]
	});

    
    
	$('#show_emails_table').on('click', 'tr', function(event) {
		
		if(!$(event.target).hasClass("bbe_prevent_default") && !$(event.target).hasClass("priority-switch"))
		{
			if($(this).find(".email_subject").length)
			{
				bbe_bugs_show_email_full($($(this).find(".email_subject")[0]).attr("id").replace("email_element_", ""));
			}
		}
		else
		{
			if($(event.target).hasClass("priority-switch"))
			{
				email_id = $(this).find(".email_subject").attr("id").replace("email_element_", "");
				
				if($(this).find(".priority-switch").hasClass("off"))
				{
					bbe_bugs_set_deadline_ajax(1800, email_id, true);
				}					
				else
				{
					bbe_bugs_set_deadline_ajax(10800, email_id, true);
				}
			}
			else
			{
				var checkbox = $(this).find("input[type='checkbox']");
				
				if(checkbox.prop("checked")) { checkbox.prop('checked', false); }
				else { checkbox.prop("checked", true); }
				
				bbe_bugs_count_msg_to_delete();
			}
		}
		
		
		
		
	});
	
	
	dTable = $('#show_deleted_emails_table').dataTable({
        "iDisplayLength": 100,
		"bProcessing": true,
		"bServerSide": true,
//		"sAutoWidth": true,
//		"bAutoWidth": true,
		"sAjaxSource": BASE_URL + "email/mail-ajax/get-data-table/format/json/is_deleted/1",
		"bJQueryUI": false,
		"sPaginationType": "full_numbers",
		
		"drawCallback": function(){
	    	$('.has-poshytip').poshytip({
				className: 'tip-twitter',
				showTimeout: 1,
				alignTo: 'target',
				alignX: 'center',
				offsetY: 5,
				allowTipHover: false,
				fade: false,
				slide: false
			});
		},
		
		
		"aoColumns": [
						{ "bSortable": true, "sWidth": "5%" },
						{ "bSortable": true, "sWidth": "15%" },
						{ "bSortable": true, "sWidth": "49%" },
						{ "bSortable": false, "sWidth": "1%" },
						{ "bSortable": true, "sWidth": "20%" },
						{ "bSortable": true, "sWidth": "10%" }
		],
		"order": [[ 0, "desc" ]]
	});

	$('#show_deleted_emails_table').on('click', 'tr', function(event) {
		bbe_bugs_show_email_full($($(this).find(".email_subject")[0]).attr("id").replace("email_element_", ""));
	});
	
	
	if (window.history && window.history.pushState) {

		window.location.hash = "inbox";

		$(window).on('popstate', function() {
			if(window.location.hash == "#inbox")
			{
				bbe_bugs_show_email_list();
			}
			else if(window.location.hash.substr(0, 6) == "#full_")
			{
				bbe_bugs_show_email_full(window.location.hash.replace("#full_", ""), "default");
			}
			else if(window.location.hash.substr(0, 5) == "#fwd_")
			{
				bbe_bugs_show_email_full(window.location.hash.replace("#fwd_", ""), "fwd");
			}
		});
	}
	
	setInterval(function () {
		bbe_redraw_email_tables();
	}, 60000);
	
	bbe_check_autoload_email_on_startup();
	
} );



function bbe_check_autoload_email_on_startup()
{
	var autoload_email_id = $("#autoload_email_id").val();
	if(autoload_email_id)
	{
		bbe_bugs_show_email_full(autoload_email_id, "read");
		$("#autoload_email_id").val("");
	}
}


function bbe_redraw_email_tables()
{
	$(".tip-twitter").remove();
	oTable.fnDraw();
	dTable.fnDraw();
}


function bbe_bugs_delete_multi_emails()
{
	if($(".bbe_bugs_email_multicheck:checked").length > 0)
	{
		bootbox.dialog({
			message: '<textarea id="bbe_bugs_must_justify_msg_delete_textarea" class="form-control" rows="4"></textarea>',
			title: "Reason for deleting",
			buttons: {
				main: {
					label: "Submit",
					className: "btn-primary",
					callback: function() {
						var reason = $("#bbe_bugs_must_justify_msg_delete_textarea").val();
						if(reason)
						{
							bbe_bugs_delete_multi_emails_ajax(reason);
						}
						else
						{
							return false;
						}
					}
				}
			}
		});
	}
}


function bbe_bugs_delete_multi_emails_ajax(reason)
{
	$.ajax({
		type		: 'POST',
		url			: BASE_URL + "email/mail-ajax/multi-delete/format/json",
		data		: {
			email_ids: checkedEmails,
			reason: reason
		},
		dataType	: 'json',
		success		: function(json)
		{
			checkedEmails = [];
			bs_notification(json.title, json.msg, json.msg_type);
			bbe_bugs_show_email_list();
		},
		error		: function( objRequest )
		{
			bs_notification("Error:", "Something went wrong!", "danger");
		}
	});
}

function bbe_bugs_show_email_full(id, open_type)
{
	$.ajax({
		type		: 'POST',
		url			: BASE_URL + "email/mail-ajax/show-full-email/format/html",
		data		: {id: id},
		dataType	: 'html',
		success		: function(html)
		{
			// window.location.hash = "full_" + id;
			$("#show_email_full_wrap").html(html);
			$("#show_emails_table_wrap").hide();
			$("#show_email_full_wrap").show();
			
			window.scrollTo(0, 0);
			
			setTimeout(function(){
				tinymce.init({
				    selector: "#bbe_bugs_reply_wrap textarea",
				    menubar : false,
				    plugins: [
				  	        "textcolor"
			  	    ],
			  	    /*setup : function(ed) { 
						ed.addButton('send_mail', {
							text: 'Send',
							title : 'Send mail', 
							onclick : function() {
								bbe_bugs_send_email();
							} 
						});
					},*/
			  	    height: 300,
			  	    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
			  	    toolbar: "send_mail | insertfile undo redo | styleselect | bold italic | forecolor backcolor fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | print preview media | link image"
				});
				
				if(open_type == "fwd")
				{
					bbe_bugs_email_reply(id, true);
					$("#reply_action_type").val("forward");
				}
				else if(open_type == "compose")
				{
					bbe_bugs_email_reply(id, true);
					$("#bbe_bugs_email_subject_field").val("");
					$("#bbe_bugs_email_fields_top").hide();
					$("#bbe_bugs_email_original_message_wrap").hide();
					$("#reply_action_type").val("compose");
				}
				else
				{
					$("#reply_action_type").val("reply");
				}
				
				$('.has-poshytip').poshytip({
					className: 'tip-twitter',
					showTimeout: 1,
					alignTo: 'target',
					alignX: 'center',
					offsetY: 5,
					allowTipHover: false,
					fade: false,
					slide: false
				});
				
				
				$("#bbe_bugs_email_to_field_input_text").bbe_autocomplete({
					url: BASE_URL + "email/contacts/get-from-bbe/format/json",
					is_extract_email: true
				});
				
				$("#bbe_bugs_email_cc_field_input_text").bbe_autocomplete({
					url: BASE_URL + "email/contacts/get-from-bbe/format/json",
					is_extract_email: true
				});
				
				$("#bbe_bugs_email_bcc_field_input_text").bbe_autocomplete({
					url: BASE_URL + "email/contacts/get-from-bbe/format/json",
					is_extract_email: true
				});
				
				
				$("#bbe_bugs_email_to_field_input input").keyup(function (e) {
					if (e.keyCode == 13) {
						bbe_bugs_add_email_field_input('to');
					}
					else if (e.keyCode == 27){
						$("#bbe_bugs_email_to_field_input").hide();
					}
				});
				
				$("#bbe_bugs_email_cc_field_input input").keyup(function (e) {
					if (e.keyCode == 13) {
						bbe_bugs_add_email_field_input('cc');
					}
					else if (e.keyCode == 27){
						$("#bbe_bugs_email_cc_field_input").hide();
					}
				});
				
				$("#bbe_bugs_email_bcc_field_input input").keyup(function (e) {
					if (e.keyCode == 13) {
						bbe_bugs_add_email_field_input('bcc');
					}
					else if (e.keyCode == 27){
						$("#bbe_bugs_email_bcc_field_input").hide();
					}
				});
				
				
				$("#bbe_bugs_email_to_field_input input").focusout(function () {
					if($("#bbe_bugs_email_to_field_input input").val().length == 0)
					{
						$("#bbe_bugs_email_to_field_input").hide();
					}
					else
					{
						bbe_bugs_add_email_field_input('to');
					}
				});
				$("#bbe_bugs_email_cc_field_input input").focusout(function () {
					if($("#bbe_bugs_email_cc_field_input input").val().length == 0)
					{
						$("#bbe_bugs_email_cc_field_input").hide();
					}
					else
					{
						bbe_bugs_add_email_field_input('cc');
					}
				});
				$("#bbe_bugs_email_bcc_field_input input").focusout(function () {
					if($("#bbe_bugs_email_bcc_field_input input").val().length == 0)
					{
						$("#bbe_bugs_email_bcc_field_input").hide();
					}
					else
					{
						bbe_bugs_add_email_field_input('bcc');
					}
				});
				
				$("#bbe_bugs_set_deadline_select").change(function(){
					if($(this).val() == "custom")
					{
						$("#bbe_bugs_set_deadline_input_option_custom_wrap").show();
					}
					else
					{
						$("#bbe_bugs_set_deadline_input_option_custom_wrap").hide();
					}
				});
				
				if($("#bbe_bugs_email_assign_paper_name_warp").html().trim() == "-")
				{
					bbe_bugs_get_pubbs_from_bbe();
				}
				else
				{
					bbe_check_assign_pubb_status();
				}
				
			}, 500);
			
		},
		error		: function( objRequest )
		{
			
		}
	});
	
}


function bbe_check_assign_pubb_status()
{
	$("#bbe_bugs_pubb_find_btn").removeClass("btn-primary");
	
	if($("#bbe_bugs_email_assign_paper_name_warp").html().trim() == "-")
	{
		$("#bbe_bugs_pubb_find_btn").addClass("btn-primary");
	}
}


function bbe_bugs_send_email()
{
	bs_notification("In progress:", "sending email...", "info");
	$.ajax({
		type		: 'POST',
		url			: BASE_URL + "email/mail-ajax/send-reply/format/json",
		data		: {
			// id: window.location.hash.replace("#full_", "").replace("#fwd_", ""),
			id: $("#bbe_bugs_email_id").val(),
			subject: $("#bbe_bugs_email_subject_field").val(),
			content: tinyMCE.activeEditor.getContent(),
			email_to: $("#bbe_bugs_email_to_field").val(),
			email_cc: $("#bbe_bugs_email_cc_field").val(),
			email_bcc: $("#bbe_bugs_email_bcc_field").val(),
			action_type: $("#reply_action_type").val()
		},
		dataType	: 'json',
		success		: function(json)
		{
			bs_notification(json.title, json.msg, json.msg_type);
			if((json.msg_type == "success") && ($("#reply_action_type").val() != "compose"))
			{
				bbe_bugs_show_email_full($("#bbe_bugs_email_id").val(), "read");
			}
			else if($("#reply_action_type").val() == "compose")
			{
				bbe_bugs_show_email_list();
			}
		},
		error		: function( objRequest )
		{
			bs_notification("Error:", "Something went wrong!", "danger");
		}
	});
}


function bbe_bugs_show_email_list()
{
	bbe_bugs_count_msg_to_delete();
	
	bbe_redraw_email_tables();
	$("#show_emails_table_wrap").show();
	$("#show_email_full_wrap").hide();
	
	window.location.hash = "inbox";
}


function bbe_bugs_email_delete(id)
{
	if(parseInt($("#bbe_bugs_must_justify_msg_delete").val()) == 1)
	{
		bootbox.dialog({
			message: '<textarea id="bbe_bugs_must_justify_msg_delete_textarea" class="form-control" rows="4"></textarea>',
			title: "Reason for deleting",
			buttons: {
				main: {
					label: "Submit",
					className: "btn-primary",
					callback: function() {
						var reason = $("#bbe_bugs_must_justify_msg_delete_textarea").val();
						if(reason)
						{
							bbe_edit_mail_properties(id, "delete", reason);
						}
						else
						{
							return false;
						}
					}
				}
			}
		});
	}
	else
	{
		bbe_edit_mail_properties(id, "delete");
	}
}

function bbe_bugs_email_undelete(id)
{
	bbe_edit_mail_properties(id, "undelete");
}

function bbe_bugs_email_read(id)
{
	bbe_edit_mail_properties(id, "read");
}

function bbe_bugs_email_unread(id)
{
	bbe_edit_mail_properties(id, "unread");
}

function bbe_bugs_email_reply(id, is_fwd)
{
	if($("#bbe_bugs_email_id").val())
	{
		if($("#bbe_bugs_email_assign_paper_name_warp").html().trim() == "-")
		{
			bbe_bugs_get_pubbs_from_bbe();
			bs_notification("Warning", "You must assign a paper!", "danger");
			return false;
		}
	}
	
	var subject = $("#bbe_bugs_email_subject_span").html();
	$('#bbe_bugs_email_log').hide();
	
	$("#bbe_bugs_email_to_field_input").hide();
	$("#bbe_bugs_email_cc_field_input").hide();
	$("#bbe_bugs_email_bcc_field_input").hide();
	
	if(is_fwd)
	{
		$("#bbe_bugs_email_to_field").val("");
		$("#bbe_bugs_email_cc_field").val("");
		
		$("#bbe_bugs_email_subject_field").val("Fwd:"+subject);
	}
	else
	{
		$("#bbe_bugs_email_to_field").val($("#bbe_bugs_email_to_field_original").val());
		$("#bbe_bugs_email_cc_field").val($("#bbe_bugs_email_cc_field_original").val());
		
		$("#bbe_bugs_email_subject_field").val("RE:"+subject);
	}
	bbe_bugs_transform_email_contacts_to_bs();
	$("#bbe_bugs_reply_wrap").show();
	if(!is_fwd)
	{
		tinymce.execCommand('mceFocus',false,'id_of_textarea');
	}
	else
	{
		$('#bbe_bugs_email_to_field_input').show().find('input').focus();
	}
	
	$("#bbe_bugs_email_body").removeClass("bbe_bugs_email_body_no_border");
	
	window.scrollTo(0, 0);
}

function bbe_bugs_email_forward(id)
{
	$('#bbe_bugs_email_log').hide();
	if(window.location.hash != "#fwd_" + id)
	{
		window.location.hash = "fwd_" + id;
	}
	else
	{
		window.location.hash = "full_" + id;
		window.location.hash = "fwd_" + id;
	}
}


function bbe_edit_mail_properties(id, action_type, extrainfo)
{
	$.ajax({
		type		: 'POST',
		url			: BASE_URL + "email/mail-ajax/edit-mail-properties/format/json",
		data		: {id: id, action_type: action_type, extrainfo: extrainfo},
		dataType	: 'json',
		success		: function(json)
		{
			bs_notification(json.title, json.message, json.msg_type);
			
			if(action_type=="delete")
			{
				bbe_bugs_show_email_list();
			}
			else
			{
				bbe_bugs_show_email_full($("#bbe_bugs_email_id").val(), "read");
			}
		},
		error		: function( objRequest )
		{
			bs_notification(json.title, json.message, json.msg_type);
			bbe_bugs_show_email_full($("#bbe_bugs_email_id").val(), "read");
		}
	});
}

function bbe_bugs_transform_email_contacts_to_bs()
{
	$(".bbe_bugs_email_contact_element_wrap").remove();
	var selectors = ["#bbe_bugs_email_to_field", "#bbe_bugs_email_cc_field", "#bbe_bugs_email_bcc_field"];
	
	$.each(selectors, function(k, selector){
		var html = '<div class="btn-group bbe_bugs_email_contact_element_wrap">';
		$.each($(selector).val().split(","), function(k1, email){
			email = email.trim();
//			email = email.replace('"', "");
			if(email!="")
			{
				email_full = $('<div/>').text(email).html();
				
				html += 
				'<div class="btn-group bbe_bugs_email_contact_element">'+
					'<div class="btn-group btn-group-xs">'+
						'<button type="button" class="btn btn-default" onclick="bbe_bugs_edit_email_contact('+parseInt(k1)+', \''+selector+'\');"><span class="glyphicon glyphicon-pencil"> '+ email_full +'</button>'+
						'<button class="btn btn-default" onclick="bbe_bugs_remove_email_contact('+parseInt(k1)+', \''+selector+'\');"><span class="glyphicon glyphicon-remove"></span>'+
					'</div>'+
				'</div>';
				
			}
		});
		html += '</div>';
		$(selector).after(html);
	});
}

function bbe_bugs_remove_email_contact(element_no, selector)
{
	var elements = $(selector).val().split(",");
	elements.splice(element_no, 1);
	$(selector).val(elements.join(","));
	
	bbe_bugs_transform_email_contacts_to_bs();
}

function bbe_bugs_add_email_field_input(email_type)
{
	var allEmailsSelector = "#bbe_bugs_email_" + email_type + "_field";
	var emailWrapSelector = "#bbe_bugs_email_" + email_type + "_field_input";
	var emailSelector = emailWrapSelector + " input";
	var emailAddress = $(emailSelector).val()
	if(isValidEmailAddress(emailAddress))
	{
		var elements = $(allEmailsSelector).val().split(",");
		elements.push(emailAddress);
		$(allEmailsSelector).val(elements.join(","));
		
		bbe_bugs_transform_email_contacts_to_bs();
		$(emailSelector).val("");
		$(emailWrapSelector).hide();
		$(emailSelector).poshytip('hide');
	}
	else
	{
		$(emailSelector).poshytip('show');
//		bs_notification("Error", "Please provide a valid email address", "warning");
	}
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
}

function bbe_bugs_edit_email_contact(element_no, selector)
{
	var elements = $(selector).val().split(",");
	
	el = elements.splice(element_no, 1);
	
	if(el.length)
	{
		email = extractEmails(el[0]);
		if(email)
		{
			email_type = selector.replace("#bbe_bugs_email_", "").replace("_field", "");
			$("#bbe_bugs_email_" + email_type + "_field_input").show();
			$("#bbe_bugs_email_" + email_type + "_field_input input").val(email).focus();
			$(selector).val(elements.join(","));
			bbe_bugs_transform_email_contacts_to_bs();
		}
	}
	
	
	
}

function extractEmails(text)
{
    return text.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi);
}


function bbe_bugs_get_pubbs_from_bbe()
{
	$("#bbe_bugs_pubb_find_btn").hide();
	$("#bbe_bugs_pubb_loading_wheel").show();
	
	if($("#bbe_bugs_pubb_assign option").length)
	{
		$("#bbe_bugs_pubb_assign_ext_wrap").show("fast", function(){
			$('#bbe_bugs_pubb_assign_chosen').trigger('mousedown');
		});
		$("#bbe_bugs_pubb_loading_wheel").hide();
		
	}
	else
	{
		$.ajax({
			type		: 'POST',
			url			: BASE_URL + "pubb/ajax/get-all/format/json",
			data		: {
				email_from: $("#bbe_bugs_email_from_holder").html()
			},
			dataType	: 'json',
			success		: function(json)
			{
				$("#bbe_bugs_pubb_loading_wheel").hide();
				$("#bbe_bugs_pubb_assign_ext_wrap").show();
				$("#bbe_bugs_pubb_assign_wrap").append(' <select multiple="multiple" onchange="bbe_bugs_pubb_assign_change();" data-placeholder="Choose one or more publications..." id="bbe_bugs_pubb_assign"></select>');
				$("#bbe_bugs_pubb_assign").append('<option value=""></option>');
				$("#bbe_bugs_pubb_assign").append('<option value="__no_paper__">No paper assignable [__no_paper__]</option>');
				
				$.each(json, function(label, elements){
					$("#bbe_bugs_pubb_assign").append('<optgroup id="bbe_bugs_pubb_assign-' + label + '" label="' + label + ' Publications"></optgroup>');
						$.each(elements,function(key, value){
							$("#bbe_bugs_pubb_assign-" + label).append('<option value="' + key + '">' + value + '</option>');
						});
					$("#bbe_bugs_pubb_assign").append('');
				});
				
				$.each($("#bbe_bugs_email_assign_paper_name_warp").html().trim().split(", "), function(){
					$("#bbe_bugs_pubb_assign option[value="+this+"]").prop("selected", true);
				})
				
				$("#bbe_bugs_pubb_assign").chosen();
				$('#bbe_bugs_pubb_assign_chosen').trigger('mousedown');
				
				
				
				$("#bbe_bugs_pubb_assign").change(function(){
//					bbe_bugs_confirm_pubb_from_bbe();
				});
			},
			error		: function( objRequest )
			{
				
			}
		});
	}
}



function bbe_bugs_pubb_assign_change()
{
	if($("#bbe_bugs_pubb_assign").val() && ($("#bbe_bugs_pubb_assign").val().length == 1))
	{
		bbe_bugs_confirm_pubb_from_bbe();
	}
	
	$("#bbe_bugs_pubb_assign_chosen input[type='text']").focus();
}


function bbe_bugs_hide_pubbs_from_bbe()
{
	$("#bbe_bugs_pubb_find_btn").show();
	$("#bbe_bugs_pubb_assign_ext_wrap").hide();
}


function bbe_bugs_confirm_pubb_from_bbe()
{
	var psetup = $("#bbe_bugs_pubb_assign").val();
	var pubb_name = $("#bbe_bugs_pubb_assign option[value='" + psetup + "']").text();
	
	$.ajax({
		type		: 'POST',
		url			: BASE_URL + "email/mail-ajax/set-pubb/format/json",
		data		: {
			id: $("#bbe_bugs_email_id").val(),
			psetup: psetup
			/*,
			pubb_name: pubb_name
			*/
		},
		dataType	: 'json',
		success		: function(json)
		{
			window.location.hash = "full_" + $("#bbe_bugs_email_id").val();
			bbe_bugs_show_email_full($("#bbe_bugs_email_id").val());
			bs_notification(json.title, json.msg, json.msg_type);
			
//			bbe_bugs_hide_pubbs_from_bbe();
//			$("#bbe_bugs_email_assign_paper_name_warp").html(((psetup != null) ? psetup.join(", ") : "-"));
//			$("#bbe_bugs_email_reply_btn").addClass("btn-primary");
//			$("#bbe_bugs_email_fwd_btn").addClass("btn-primary");
//			$("#bbe_bugs_pubb_find_btn").removeClass("btn-primary");
		},
		error		: function( objRequest )
		{
			
		}
	});
	
	
}


function bbe_bugs_count_msg_to_delete()
{
	if($(".bbe_bugs_email_multicheck:checked").length > 0)
	{
		$("#bbe_bugs_delete_multi_emails_btn").addClass("btn-danger");
		$("#bbe_bugs_delete_multi_emails_label").html("(" + $(".bbe_bugs_email_multicheck:checked").length + " item" + (($(".bbe_bugs_email_multicheck:checked").length > 1) ? "s" : "") + ")");
		
		checkedEmails = [];
		
		$.each($(".bbe_bugs_email_multicheck:checked"), function(){
			checkedEmails.push($(this).attr("id").replace("bbe_bugs_email_multicheck_", ""));
		});
	}
	else
	{
		checkedEmails = [];
		
		$("#bbe_bugs_delete_multi_emails_btn").removeClass("btn-danger");
		$("#bbe_bugs_delete_multi_emails_label").html("");
	}
}


function bbe_bugs_compose_new_email()
{
	bbe_bugs_show_email_full(0, "compose");
}

function bbe_bugs_set_deadline()
{
	$("#bbe_bugs_set_deadline_input_option_custom_wrap").show();
	
	$("#bbe_bugs_set_deadline_input_option_custom_date").datepicker({
		format: "yyyy-mm-dd"
	});
	
	$("#bbe_bugs_set_deadline_input_option_custom_time").timepicker({
		showMeridian: false
	});
	
}

function bbe_bugs_undo_set_deadline()
{
	$("#bbe_bugs_set_deadline_select_wrap").hide();
	$("#bbe_bugs_set_deadline_btn").show();
}

function bbe_bugs_set_deadline_ajax(deadline, id, is_refresh_email_list)
{
	if(!id)
	{
		id = $("#bbe_bugs_email_id").val();
	}
	
	$.ajax({
		type		: 'POST',
		url			: BASE_URL + "email/mail-ajax/set-deadline/format/json",
		data		: {
			id: id,
			deadline: deadline,
			custom_date: $("#bbe_bugs_set_deadline_input_option_custom_date").val(),
			custom_time: $("#bbe_bugs_set_deadline_input_option_custom_time").val()
		},
		dataType	: 'json',
		success		: function(json)
		{
			if(is_refresh_email_list)
			{
				oTable.fnDraw();
				dTable.fnDraw();
			}
			else
			{
				bbe_bugs_undo_set_deadline();
				bs_notification(json.title, json.msg, json.msg_type);
				bbe_bugs_show_email_full($("#bbe_bugs_email_id").val());
			}
			
		},
		error		: function( objRequest )
		{
		}
	});
}

function bbe_bugs_scroll_to_full_mail_attachments_header()
{
	$('html, body').animate({
        scrollTop: $("#bbe_bugs_full_mail_attachments_header").offset().top
    }, 100);
}