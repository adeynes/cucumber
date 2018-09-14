<?php
declare(strict_types=1);

namespace adeynes\cucumber\utils;

interface Queries
{

    public const CUCUMBER_ADD_PLAYER = "cucumber.add.player";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:71
     *
     * <h3>Variables</h3>
     * - <code>:name</code> string, required in mysql.sql
     */
    public const CUCUMBER_GET_PLAYER_BY_NAME = "cucumber.get.player.by-name";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:79
     */
    public const CUCUMBER_GET_PUNISHMENTS_BANS_ALL = "cucumber.get.punishments.bans.all";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:85
     */
    public const CUCUMBER_GET_PUNISHMENTS_BANS_CURRENT = "cucumber.get.punishments.bans.current";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:90
     */
    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_ALL = "cucumber.get.punishments.ip-bans.all";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:94
     */
    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_CURRENT = "cucumber.get.punishments.ip-bans.current";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:104
     */
    public const CUCUMBER_GET_PUNISHMENTS_MUTES_ALL = "cucumber.get.punishments.mutes.all";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:110
     */
    public const CUCUMBER_GET_PUNISHMENTS_MUTES_CURRENT = "cucumber.get.punishments.mutes.current";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:98
     */
    public const CUCUMBER_GET_PUNISHMENTS_UBANS = "cucumber.get.punishments.ubans";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:12
     */
    public const CUCUMBER_INIT_PLAYERS = "cucumber.init.players";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:24
     */
    public const CUCUMBER_INIT_PUNISHMENTS_BANS = "cucumber.init.punishments.bans";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:34
     */
    public const CUCUMBER_INIT_PUNISHMENTS_IP_BANS = "cucumber.init.punishments.ip-bans";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:54
     */
    public const CUCUMBER_INIT_PUNISHMENTS_MUTES = "cucumber.init.punishments.mutes";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:43
     */
    public const CUCUMBER_INIT_PUNISHMENTS_UBANS = "cucumber.init.punishments.ubans";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:122
     *
     * <h3>Variables</h3>
     * - <code>:expiration</code> int, required in mysql.sql
     * - <code>:moderator</code> string, required in mysql.sql
     * - <code>:reason</code> string, required in mysql.sql
     * - <code>:name</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_BAN = "cucumber.punish.ban";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:139
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
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:143
     *
     * <h3>Variables</h3>
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_IP_UNBAN = "cucumber.punish.ip-unban";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:158
     *
     * <h3>Variables</h3>
     * - <code>:expiration</code> int, required in mysql.sql
     * - <code>:moderator</code> string, required in mysql.sql
     * - <code>:reason</code> string, required in mysql.sql
     * - <code>:name</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_MUTE = "cucumber.punish.mute";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:150
     *
     * <h3>Variables</h3>
     * - <code>:moderator</code> string, required in mysql.sql
     * - <code>:reason</code> string, required in mysql.sql
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_UBAN = "cucumber.punish.uban";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:131
     *
     * <h3>Variables</h3>
     * - <code>:name</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_UNBAN = "cucumber.punish.unban";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:167
     *
     * <h3>Variables</h3>
     * - <code>:name</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_UNMUTE = "cucumber.punish.unmute";

}