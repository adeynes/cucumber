<?php
declare(strict_types=1);

namespace adeynes\cucumber\utils;

interface Queries
{
    
    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:68
     *
     * <h3>Variables</h3>
     * - <code>:name</code> string, required in mysql.sql
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_ADD_PLAYER = "cucumber.add.player";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:75
     *
     * <h3>Variables</h3>
     * - <code>:name</code> string, required in mysql.sql
     */
    public const CUCUMBER_GET_PLAYER_BY_NAME = "cucumber.get.player.by-name";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:83
     */
    public const CUCUMBER_GET_PUNISHMENTS_BANS_ALL = "cucumber.get.punishments.bans.all";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:108
     *
     * <h3>Variables</h3>
     * - <code>:player</code> string, required in mysql.sql
     */
    public const CUCUMBER_GET_PUNISHMENTS_BANS_BY_PLAYER = "cucumber.get.punishments.bans.by-player";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:101
     */
    public const CUCUMBER_GET_PUNISHMENTS_BANS_COUNT = "cucumber.get.punishments.bans.count";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:89
     */
    public const CUCUMBER_GET_PUNISHMENTS_BANS_CURRENT = "cucumber.get.punishments.bans.current";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:98
     *
     * <h3>Variables</h3>
     * - <code>:limit</code> int, required in mysql.sql
     * - <code>:from</code> int, required in mysql.sql
     */
    public const CUCUMBER_GET_PUNISHMENTS_BANS_LIMITED = "cucumber.get.punishments.bans.limited";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:113
     */
    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_ALL = "cucumber.get.punishments.ip-bans.all";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:132
     *
     * <h3>Variables</h3>
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_BY_IP = "cucumber.get.punishments.ip-bans.by-ip";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:127
     */
    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_COUNT = "cucumber.get.punishments.ip-bans.count";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:117
     */
    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_CURRENT = "cucumber.get.punishments.ip-bans.current";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:124
     *
     * <h3>Variables</h3>
     * - <code>:limit</code> int, required in mysql.sql
     * - <code>:from</code> int, required in mysql.sql
     */
    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_LIMITED = "cucumber.get.punishments.ip-bans.limited";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:142
     */
    public const CUCUMBER_GET_PUNISHMENTS_MUTES_ALL = "cucumber.get.punishments.mutes.all";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:167
     *
     * <h3>Variables</h3>
     * - <code>:player</code> string, required in mysql.sql
     */
    public const CUCUMBER_GET_PUNISHMENTS_MUTES_BY_PLAYER = "cucumber.get.punishments.mutes.by-player";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:160
     */
    public const CUCUMBER_GET_PUNISHMENTS_MUTES_COUNT = "cucumber.get.punishments.mutes.count";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:148
     */
    public const CUCUMBER_GET_PUNISHMENTS_MUTES_CURRENT = "cucumber.get.punishments.mutes.current";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:157
     *
     * <h3>Variables</h3>
     * - <code>:limit</code> int, required in mysql.sql
     * - <code>:from</code> int, required in mysql.sql
     */
    public const CUCUMBER_GET_PUNISHMENTS_MUTES_LIMITED = "cucumber.get.punishments.mutes.limited";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:136
     */
    public const CUCUMBER_GET_PUNISHMENTS_UBANS = "cucumber.get.punishments.ubans";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:12
     */
    public const CUCUMBER_INIT_PLAYERS = "cucumber.init.players";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:25
     */
    public const CUCUMBER_INIT_PUNISHMENTS_BANS = "cucumber.init.punishments.bans";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:36
     */
    public const CUCUMBER_INIT_PUNISHMENTS_IP_BANS = "cucumber.init.punishments.ip-bans";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:58
     */
    public const CUCUMBER_INIT_PUNISHMENTS_MUTES = "cucumber.init.punishments.mutes";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:46
     */
    public const CUCUMBER_INIT_PUNISHMENTS_UBANS = "cucumber.init.punishments.ubans";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:181
     *
     * <h3>Variables</h3>
     * - <code>:expiration</code> int, required in mysql.sql
     * - <code>:moderator</code> string, required in mysql.sql
     * - <code>:player</code> string, required in mysql.sql
     * - <code>:reason</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_BAN = "cucumber.punish.ban";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:198
     *
     * <h3>Variables</h3>
     * - <code>:expiration</code> int, required in mysql.sql
     * - <code>:moderator</code> string, required in mysql.sql
     * - <code>:reason</code> string, required in mysql.sql
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_IP_BAN = "cucumber.punish.ip-ban";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:202
     *
     * <h3>Variables</h3>
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_IP_UNBAN = "cucumber.punish.ip-unban";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:219
     *
     * <h3>Variables</h3>
     * - <code>:expiration</code> int, required in mysql.sql
     * - <code>:moderator</code> string, required in mysql.sql
     * - <code>:player</code> string, required in mysql.sql
     * - <code>:reason</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_MUTE = "cucumber.punish.mute";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:209
     *
     * <h3>Variables</h3>
     * - <code>:moderator</code> string, required in mysql.sql
     * - <code>:reason</code> string, required in mysql.sql
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_UBAN = "cucumber.punish.uban";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:190
     *
     * <h3>Variables</h3>
     * - <code>:player</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_UNBAN = "cucumber.punish.unban";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:228
     *
     * <h3>Variables</h3>
     * - <code>:player</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_UNMUTE = "cucumber.punish.unmute";

}