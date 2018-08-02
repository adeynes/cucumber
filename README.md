# cucumber

[![](https://poggit.pmmp.io/shield.state/cucumber)](https://poggit.pmmp.io/p/cucumber)

**cucumber** is a featureful moderation plugin for PocketMine-MP.
New features are always being developed. As of now, cucumber's main components are `1` logging and `2` punishment (ban/mute) management.

### Logging
cucumber logs various events¹ to a path specified in `config.yml`. Several loggers (including custom ones) can also be defined in `config.yml`. (By default, a BaseLogger exists that logs to `log_out.txt`.)
*Upcoming feature: toggle different log categories (traffic, chat, command)*

¹ cucumber logs the following: join, join attempt (if a player attempts to join while banned), quit, chat, chat attempt (if a player attempts to chat while muted), command

### Punishment Management
cucumber enables advanced banning of players and IPs, as well as muting.

| **Command**  | **Description**         | **Usage**                                                 | **Tags**                                                                                                                                                                                                   |
|--------------|-------------------------|-----------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `/ban`       | Ban a player by name    | `/ban <player> [reason] [-d <duration>]`                  | `-d` specifies the amount of time until the ban expires                                                                                                                                                    |
| `/banlist`   | See the list of bans    | `/banlist`                                                |                                                                                                                                                                                                            |
| `/pardon`    | Pardon a player         | `/pardon <player>`<br>`/unban <player>`                   |                                                                                                                                                                                                            |
| `/ipban`     | Ban an IP               | `/ipban <-p <player>\|-ip <ip>> [reason] [-d <duration>]` | `-p` specifies the player whose IP will be banned<br>`-ip` specifies the IP that will be banned<br>*At least one of `-p` and `-ip` must be set*<br>`-d` specifies the amount of time until the ban expires |
| `/ipbanlist` | See the list of IP bans | `/ipbanlist`                                              |                                                                                                                                                                                                            |
| `/ippardon`  | Pardon an IP            | `/ippardon <ip>`<br>`/ipunban <ip>`                       |                                                                                                                                                                                                            |
| `/mute`      | Mute a player           | `/mute <player> [reason] [-d <duration>]`                 | `-d` specifies the amount of time until the ban expires                                                                                                                                                    |
| `/mutelist`  | See the list of mutes   | `mutelist`                                                |                                                                                                                                                                                                            |
| `/unmute`    | Unmute a player         | `/unmute <player>`                                        |                                                                                                                                                                                                            |

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

| **Command** | **Description**                   | **Usage**                                      | **Tags**                                                                        |
|-------------|-----------------------------------|------------------------------------------------|---------------------------------------------------------------------------------|
| `/rawtell`  | Send a raw message to a player    | `/rawtell <player> <message> [-nom] [-p] [-t]` | `-nom` does not send a chat message<br>`-p` sends a popup<br>`-t`sends a title  |
| `/log`      | Log a message                     | `/log <message> [-s <severity>]`               | `-s` specifies the severity at which to log the message                         |
| `/alert`    | Broadcast a message to the server | `/alert <message> [-nom] [-p] [-t]`            | `-nom` does not send a chat message<br>`-p` sends a popup<br>`-t` sends a title |
| `/ip`       | Get a player's IP                 | `/ip <player>`                                 |                                                                                 |

#### Severity
In a severity tag:
* `log`: Normal, ordinary events
* `notice`: Normal but noticeable events
* `important`: Significant events
* `alert`: Exceptional events that likely require monitoring