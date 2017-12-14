# pesstatsdatabase-API
###### PHP wrapper for pesstatsdatabase.com which exposes simple to use, GET api
\
This version is hosted at heroku (free account, so please be gentle with the usage)
**[HERE: pesstatsdatabasedapi.herokuapp.com/index.php](https://pesstatsdatabasedapi.herokuapp.com/index.php)**

__Usage:__
Send GET request to index.php with v,t,p or s parameters. Send request without parameters to see usage.

__Parameters__
 * v = ??? - GAME VERSION (INTEGER, 6=pes6, 2018=pes18)
 * p = ??? - playerId to fetch 
 * s = ??? - playerName to search
 * t = ??? - teamId to fetch
 * n = ??? - teamName to search

__Example workflow:__
1) Search for Real Madrid's teamId  
__Request:__` index.php?n=Real%20Madrid `  
__Response:__ ` 33; Real Madrid CF`  
(Format: `teamId; teamName`)  

2) Find all players in Real Madrid  
__Request:__` index.php?t=33 `  
__Response:__` Real Madrid CF `  
`1 - Keylor Navas; 10728; `  
` ...`  
(Format: `shirtNo - playerName; playerId`)  

3) Find playerId by name  
__Request:__`index.php?s=Cristiano%20Ronaldo`  
__Response:__` Cristiano Ronaldo; 774; Real Madrid CF; 33; `  
(Format: `playerName; playerId; TeamName; teamId`)  

4) Get player stats  
__Request:__`index.php?v=6&p=774`  
__Response:__ plaintext PSD stats for PES6  

