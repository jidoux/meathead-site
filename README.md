# meathead-site
A website built using HTML, CSS, PHP, and JavaScript, which tracks meathead trait drops in Hunt: Showdown and displays graphs and charts of trait drop chances. 
It shows traits dropped per day also, so you can see how many meatheads were killed on August 10th for example and what the trait drop chance was on that day.

# How to run it?
You will need to update the getPDO function in all 3 php files to your own relevant information. 
It may entail liking to some web hosting service or using something locally, such as Apache, MAMP, or Docker. 
Just do this to setup the database linking and it should work beyond that.

# For what purpose?
Meatheads are an most uncommon, but stronger AI in Hunt: Showdown. They have a 50% chance to drop a Trait, which Hunters can 
have applied to them which gives them certain powers they did not have before such as slightly less stamina consumption while running (Greyhound), 
ability to fan pistols (Fanning), ability to reload Crossbows faster (Bolt Thrower), ability to see traps in dark sight (Vigilant), ability to have all AI ignore them (Shadow), stuff like this. 
This website was inspired by a friend of mine who statistically tracked meathead trait drops on a google slides spreadsheet, and I knew I could make it on a website. 
It's point is tracking which of the many traits in the game are dropped most commonly. Do traits actually have a 50% chance to drop? Without the game's source code 
this website may be the best way to determine what the drop chance actually is. (His number his google sheet was 45.13% drop chance with 277 meatheads killed btw). 
The reason this project is discontinued is because Hunt: Showdown removed the screen at the end of the game which shows the number of meatheads killed during a game on August 15th. 
Maybe they have since added it back, I'm not sure, but it just made it harder to track this stuff and we mostly stopped doing it. 
![alt text](https://github.com/jidoux/meathead-site/blob/main/meathead-pic-7.png?raw=true)

This is just something I made over the summer of 2024. I wouldn't even use this stack now (obviously I would use a framework now but I was clueless, that'll happen). One day I will probably re-make this 
but make it actually good since it's a very interesting concept which unfortunately was kind of ruined by some hunt staff (designers) making their UI worse and thus making it a pain to track meathead kills. 
Still the best game out there in spite of some poor design choices by some people in power so I would highly recommend trying out hunt showdown to all who have read this far (not an ad just life advice).