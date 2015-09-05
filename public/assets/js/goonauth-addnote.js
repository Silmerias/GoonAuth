
$('#modal-Note').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget);
	var uid = button.data('uid');
	var goonid = button.data('goonid');

	var modal = $(this);
	modal.find('.modal-body textarea').val('');
	modal.find('#note-add').attr('data-id', uid);
	modal.find('#note-global').prop('checked', true);
	modal.find('.note-user').text('- ' + goonid);

	modal.find('#note-type').change();
	modal.find('#note-subject').val('').keyup();
	modal.find('#note-text').keyup();
	modal.find('#note-global').change();
});

$('#note-type').change(function() {
	var o = $('option', this).filter(':selected');
	$('#note-preview').css('backgroundColor', o.attr('color'));
	$('#note-preview .note-type').text(o.text());
});

$('#note-subject').keyup(function() {
	$('#note-preview .note-subject').text($(this).val());
});

$('#note-text').keyup(function() {
	var t = $(this).val();
	t = t.replace('\n', '<br>');
	$('#note-preview .note-comment').html(t);
});

$('#note-global').change(function() {
	if ($(this).prop('checked'))
		$('#note-preview .note-global').show();
	else $('#note-preview .note-global').hide();
});

$('#note-add').click(function (event) {
	var id = $(this).attr('data-id');
	var url = $(this).attr('data-url');

	$.ajax({
		url: url,
		type: 'post',
		dataType: 'json',
		data: {
			action: 'addnote',
			id: id,
			type: $('#note-type').val(),
			subject: $('#note-subject').val(),
			message: $('#note-text').val(),
			global: $('#note-global').prop('checked')
		}
	})
	.done(function(ret) {
		$('#modal-Note').modal('hide');
		if (ret.success == false)
		{
			$('#modal-Note').modal('hide');
			error(ret.message);
		}
	})
	.fail(function(ret) {
		$('#modal-Note').modal('hide');
		error('An internal server error has occurred.');
	});
});
