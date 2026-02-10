jQuery(document).ready(function ($) {
    var mediaUploader;

    $('#df_select_media').click(function (e) {
        e.preventDefault();

        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Select Images/PDF for Flip Book',
            button: {
                text: 'Add to Flip Book'
            },
            multiple: true // Allow multiple files
        });

        // When a file is selected, grab the URL and set it as the value of the text input
        mediaUploader.on('select', function () {
            var selection = mediaUploader.state().get('selection');
            var sources = $('#book_source').val();
            var urls = sources ? sources.split(',').map(s => s.trim()).filter(s => s !== '') : [];

            selection.map(function (attachment) {
                attachment = attachment.toJSON();
                urls.push(attachment.url);
            });

            $('#book_source').val(urls.join(', '));
        });

        // Open the uploader dialog
        mediaUploader.open();
    });
});
