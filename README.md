# cucumber

[![](https://poggit.pmmp.io/shield.state/cucumber)](https://poggit.pmmp.io/p/cucumber)
[![](https://poggit.pmmp.io/ci.shield/adeynes/cucumber/cucumber)](https://poggit.pmmp.io/p/cucumber)
[![](https://poggit.pmmp.io/shield.dl.total/cucumber)](https://poggit.pmmp.io/p/cucumber)

### Current stable version: 1.5.0

**cucumber** is a featureful moderation plugin for PocketMine-MP.
New features are always being developed. As of now, cucumber's main components are `1` logging and `2` punishment (ban/mute) management.

### Logging
cucumber logs various events¹ to a path specified in `config.yml`. Several loggers (including custom ones) can also be defined in `config.yml`. (By default, a BaseLogger exists that logs to `log_out.txt`.) Messages are logger every 10 seconds asynchronously. (Timestamps are preserved, they are calculated upon scheduling the logging of the message, not when the message is written to the file.)

¹ cucumber logs the following: join, join attempt (if a player attempts to join while banned), quit, chat, chat attempt (if a player attempts to chat while muted), command

### Punishment Management
cucumber enables advanced banning of players and IPs, as well as muting.

| **Command**  | **Description**                                     | **Usage**                                                                 | **Tags**                                                                                                                                                                                                                                                                                          |
|--------------|-----------------------------------------------------|---------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `/ban`       | Ban a player by name                                | `/ban <player> [reason] [-duration\|-d <duration>]`                       | `-duration` specifies the amount of time until the ban expires                                                                                                                                                                                                                                    |
| `/banlist`   | See the list of bans                                | `/banlist`                                                                |                                                                                                                                                                                                                                                                                                   |
| `/pardon`    | Pardon a player                                     | `/pardon <player>`                                                        |                                                                                                                                                                                                                                                                                                   |
| `/ipban`     | Ban an IP                                           | `/ipban <target> [reason] [-duration\|-d <duration>] [-player\|-p] [-ip]` | `-duration` specifies the amount of time until the ban expires<br>`-player` specifies that a player is to be banned<br>`-ip` specifies that an IP is to be banned<br>*If neither or both of the `-player` and `-ip` are be set, the command will infer whether a player or an IP is to be banned* |
| `/ipbanlist` | See the list of IP bans                             | `/ipbanlist`                                                              |                                                                                                                                                                                                                                                                                                   |
| `/ippardon`  | Pardon an IP                                        | `/ippardon <ip>`                                                          |                                                                                                                                                                                                                                                                                                   |
| `/uban`      | Ban any player that joins using an IP. Irreversible | `/uban <target> [reason] [-player\|-p] [-ip]`                             | `-player` specifies that a player is to be banned<br>`-ip` specifies that an IP is to be banned<br>*If neither or both of the `-player` and `-ip` are be set, the command will infer whether a player or an IP is to be banned*                                                                   |
| `/mute`      | Mute a player                                       | `/mute <player> [reason] [-duration\|-d <duration>]`                      | `-duration` specifies the amount of time until the ban expires                                                                                                                                                                                                                                    |
| `/mutelist`  | See the list of mutes                               | `/mutelist`                                                               |                                                                                                                                                                                                                                                                                                   |
| `/unmute`    | Unmute a player                                     | `/unmute <player>`                                                        |                                                                                                                                                                                                                                                                                                   |

#### Duration
In a duration tag:
* `y`: year
* `M`: month
* `w`: week
* `d`: day
* `h`: hour
* `m`: minute

For instance, `1y3M` means one year and three months (a year and a quarter). `1w2d12h` means one week, two days, and twelve hours (nine days and a half).

### Miscellaneous
cucumber also has miscellaneous moderation commands that are not related to punishment management.

| **Command** | **Description**                   | **Usage**                                                                  | **Tags**                                                                                      |
|-------------|-----------------------------------|----------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------|
| `/rawtell`  | Send a raw message to a player    | `/rawtell <player> <message> [-nomessage\|-nom] [-popup\|-p] [-title\|-t]` | `-nomessage` does not send a chat message<br>`-popup` sends a popup<br>`-title`sends a title  |
| `/log`      | Log a message                     | `/log <message> [-severity\|-s <severity>]`                                | `-severity` specifies the severity at which to log the message                                |
| `/alert`    | Broadcast a message to the server | `/alert <message> [-nomessage\|-nom] [-popup\|-p] [-title\|-t]`            | `-nomessage` does not send a chat message<br>`-popup` sends a popup<br>`-title` sends a title |
| `/ip`       | Get a player's IP                 | `/ip <player>`                                                             |                                                                                               |
| `/vanish`   | Vanish from other player's sight  | `/vanish`                                                                  |                                                                                               |

#### Severity
In a severity tag:
* `log`: Normal, ordinary events
* `notice`: Normal but noticeable events
* `important`: Significant events
* `alert`: Exceptional events that likely require monitoring