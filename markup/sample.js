module.exports =
[
    {
        template: 'news',
        data:
        {
            date: 'Friday, January 8, 2016',
            title: 'Lazy dinner party',
            message: 'Come over to fish house for a lazy dinner party! (Bring prepared foods...)'
        }
    },

    // Strings are passed automatically into the 'text' template
    'This is just some regular text',
    'Hi there how are you?',

    // Objects without a template default to 'text'
    {
        align: 'right',
        data: 'Woaohao, the text is FLOOAATTINGGG'
    },

    {
        template: 'image',
        data:
        {
            url: 'upload/34e6b536-3b66-9498-af63-468f93291baf.jpg',
            size: '400px',
            caption: 'a chicken and two snakes',
            border: true
        },

        align: 'right'
    },

    // Multiple templates can be passed as well
    {
        template: ['big', 'big', 'rainbow'],
        data: 'Yeah!! Rainbows are great :D'
    }
]
