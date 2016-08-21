// Create var parallaxObj with the IDs and desired speed for each ID.
var parallaxObj = [
	{
		id: 'div-1',
		speed: 0,
		background: 'images/1.jpg',
        top: {
        	768: '500px',
        	400: '50px',
        	0  : '200px'
        }
	},
	{
		id: 'div-2',
		speed: 0.8,
		background: 'images/2.jpg',
        top: '80%'
	},
	{
		id: 'div-3',
		speed: -0.2,
		top: '100px'
	},
	{
		id: 'div-4',
		speed: 0.5,
        top: '200px'
	},
	{
		id: 'div-5',
		speed: -0.5,
		top: '220px'
	},
];

// Start parallax
init_parallax();