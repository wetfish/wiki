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
    });
})(basic);
