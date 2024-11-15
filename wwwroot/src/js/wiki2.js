(function($)
{
    $(document).ready(function()
    {
        $('.nsfw').on('click', function()
        {
            if($(this).hasClass('show'))
            {
                $(this).removeClass('show');
            }
            else
            {
                $(this).addClass('show');
            }
        });

        $('.snip').on('click', function()
        {
            if($(this).hasClass('show'))
            {
                $(this).removeClass('show');
                $(this).find('.message a').text('Read More');
            }
            else
            {
                $(this).addClass('show');
                $(this).find('.message a').text('Show Less');
            }
        });

        addEventListener('paste', e => {
            const file = document.getElementById('upload-file');
            if (!file || !e?.clipboardData?.files?.length) return;
            file.files = e.clipboardData.files;
            file.parentNode.submit();
        });
    });
})(basic);
