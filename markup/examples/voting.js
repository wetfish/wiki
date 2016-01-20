module.exports =
[
    {
        template: 'vote',
        data:
        {
            title: 'Wow a cool video',
            author: 'rachel',
            date: 'Wednesday, November 18, 2015',
            message:
            {
                template: 'video',
                url: 'https://www.youtube.com/watch?v=ZEFsTJ0UcDA'
            },
            votes:
            {
                'HyrulianHero': ['+1', 'I liked the text to speech voice.'],
                'AtomicFiredoll': ['-1', 'I hate everything!'],
                'devnill': ['+1']
            },
            participants: 3,
            score: 2
        }
    }
]
