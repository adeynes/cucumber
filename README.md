# cucumber

[![](https://poggit.pmmp.io/shield.state/cucumber)](https://poggit.pmmp.io/p/cucumber)
[![](https://poggit.pmmp.io/ci.shield/adeynes/cucumber/cucumber)](https://poggit.pmmp.io/p/cucumber)
[![](https://poggit.pmmp.io/shield.dl.total/cucumber)](https://poggit.pmmp.io/p/cucumber)

### Current stable version: 2.0.0

**cucumber** is a complete moderation plugin for PocketMine-MP which features ban/mute management and logging of various significant events.

### Punishment Management
cucumber enables banning of players and IPs, muting and warning.

| **Command**  | **Description**                                     | **Usage**                                                                 | **Notes**                                                                                                                                                                                                        |
|--------------|-----------------------------------------------------|---------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `/ban`        | Ban a player by name                                | `/ban <player> <duration>\|inf [reason]`                                  | See below for the format of the `duration` argument                                                                                                                                                             |
| `/banlist`    | See the list of bans                                | `/banlist [-all\|-a]`                                                     | The `-all` flag specifies that all bans should be displayed, including those that have expired                                                                                                                  |
| `/pardon`     | Pardon a player                                     | `/pardon <player>`                                                        | This removes the ban from the player's punishment history and from `/banlist -all`                                                                                                                              |
| `/ipban`      | Ban an IP                                           | `/ipban <player>\|<ip> <duration>\|inf [reason]`                          | You can enter either a player or an IP as the first argument, the plugin will infer which was entered and act accordingly. See below for the format of the `duration` argumment                                 |
| `/ipbanlist`  | See the list of IP bans                             | `/ipbanlist [-all\|-a]`                                                   | The `-all` flag specifies that all IP bans should be displayed, including those that have expired                                                                                                               |
| `/ippardon`   | Pardon an IP                                        | `/ippardon <ip>`                                                          | This removes the IP ban from the player's punishment history and from `/ipbanlist -all`                                                                                                                         |
| `/uban`       | Ban any player that joins using an IP. Irreversible | `/uban <player>\|<ip> [reason]`                                           | Ubans are **permanent** and *cannot be undone*, hence they have no duration argument. You can enter either a player or an IP as the first argument, the plugin will infer which was entered and act accordingly |
| `/mute`       | Mute a player                                       | `/mute <player> <duration>\|inf [reason]`                                 | See below for the format of the `duration` argument                                                                                                                                                             |
| `/mutelist`   | See the list of mutes                               | `/mutelist [-all\|-a]`                                                    | The `-all` flag specifies that all mutes should be displayed, including those that have expired                                                                                                                 |
| `/unmute`     | Unmute a player                                     | `/unmute <player>`                                                        | This removes the mute from the player's punishment history and from `/mutelist -all`                                                                                                                            |
| `/warn`       | Warn a player                                       | `/warn <player> <duration>\|inf [reason]                                  | See below for the format of the `duration` argument                                                                                                                                                             |
| `/warnings`   | See a player's warnings                             | `/warnings <player> [-all\|-a]                                            | The `-all` flag specifies that all of the player's warnings should be displayed, including those that have expired                                                                                              |
| `/mywarnings` | See your warnings                                   | `/mywarnings [-all\|-a]                                                   | The `-all` flag specifies that all of the sender's warnings should be displayed, including those that have expired                                                                                              |
| `/delwarn`    | Delete a specific warning                           | `/delwarn <id>                                                            | Warnings are identified with an ID that should be displayed in `/warnings` and upon the creation of the warning                                                                                                 |
| `/history`    | See a player's punishment history                   | `/history <player>`                                                       | This includes expired punishments. It also includes IP bans pertaining to the player's IP. The player's first join and last join dates are also displayed                                                       |

#### Duration
In a duration argument:
* `y`: year
* `M`: month
* `w`: week
* `d`: day
* `h`: hour
* `m`: minute

For instance, `1y3M` means one year and three months (this is the same as `15M`). `1w2d12h` means one week, two days, and twelve hours (this is the same as `9d12h`).

For an infinite duration (a punishment that never expires), you can enter one of the following: `inf`, `infinite`, `perm`, `permanent`, `-1`.

### Logging
cucumber logs various events¹ to a path specified in `config.yml`. Several loggers (including custom ones) can also be defined in `config.yml`. (By default, a `BaseLogger` exists that logs everything to `log_out.txt`.) Messages are written to the file every 10 seconds asynchronously.

¹ cucumber logs the following: join, join attempt (if a player attempts to join while banned), quit, chat, chat attempt (if a player attempts to chat while muted), command

### Miscellaneous
cucumber also has miscellaneous moderation commands that are not related to punishment management.

| **Command** | **Description**                                         | **Usage**                                                                  | **Notes**                                                                                                       |
|-------------|---------------------------------------------------------|----------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------|
| `/rawtell`  | Send a raw message to a player                          | `/rawtell <player> <message> [-nomessage\|-nom] [-popup\|-p] [-title\|-t]` | `-nomessage` does not send a chat message<br>`-popup` sends a popup<br>`-title`sends a title                    |
| `/log`      | Log a message                                           | `/log <message> [-severity\|-s <severity>]`                                | `-severity` specifies the severity at which to log the message. See below for more informaiton about severities |
| `/alert`    | Broadcast a message to the server                       | `/alert <message> [-nomessage\|-nom] [-popup\|-p] [-title\|-t]`            | `-nomessage` does not send a chat message<br>`-popup` sends a popup<br>`-title` sends a title                   |
| `/ip`       | Get a player's IP and their alt accouts                 | `/ip <player>`                                                             | This shows every account that has the same IP as the player                                                     |
| `/vanish`   | Vanish from other players' sight                        | `/vanish`                                                                  |                                                                                                                 |

#### Severity
In a severity tag:
* `log`: Normal, ordinary events
* `notice`: Normal but interesting events
* `important`: Significant events
* `alert`: Exceptional events that likely require monitoring
If a message is logged at the `important` severity, for instance, loggers that listen to higher severities (ex. `alert`) will not pick it up. This is useful if you want to have an `alert` logger that sends you a Discord notification, for example.
