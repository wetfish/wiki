module.exports =
[
    {
        template: 'news',
        data:
        {
            date: 'Friday, January 8, 2016',
            title: 'Lazy dinner party',
            message:
            [
                'Come over to fish house for a lazy dinner party!',
                {
                    template: 'big',
                    data:
                    [
                        "IT'S GONNA BE",
                        {
                            template: ['big', 'rainbow'],
                            data: 'GREAT!!!!!!!!!!!!!!!!!!!!!',
                        }
                    ]
                },
                '(Bring prepared foods...)'
            ]
        }
    },

    // Note how any user input text field can become an array of nested templates
    {
        template: 'image',
        data:
        {
            url: 'upload/34e6b536-3b66-9498-af63-468f93291baf.jpg',
            size: '400px',
            caption:
            [
                {
                    template: 'underline',
                    data: 'two snakes'
                },
                {
                    template: 'bold',
                    data: 'one chicken'
                }
            ],
            border: true
        },

        align: 'right'
    },

    {
        template: ['big', 'big', 'rainbow'],
        data:
        [
            'Yeah!! Rainbows are ',
            {
                template: 'underline',
                data: 'great'
            },
            ' :D'
        ]
    }
]
