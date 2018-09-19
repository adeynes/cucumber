<?php
declare(strict_types=1);

namespace adeynes\cucumber\utils;

interface Queries
{

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:131
     *
     * <h3>Variables</h3>
     * - <code>:name</code> string, required in mysql.sql
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_ADD_PLAYER = "cucumber.add.player";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:138
     *
     * <h3>Variables</h3>
     * - <code>:name</code> string, required in mysql.sql
     */
    public const CUCUMBER_GET_PLAYER_BY_NAME = "cucumber.get.player.by-name";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:146
     */
    public const CUCUMBER_GET_PUNISHMENTS_BANS_ALL = "cucumber.get.punishments.bans.all";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:164
     */
    public const CUCUMBER_GET_PUNISHMENTS_BANS_COUNT = "cucumber.get.punishments.bans.count";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:152
     */
    public const CUCUMBER_GET_PUNISHMENTS_BANS_CURRENT = "cucumber.get.punishments.bans.current";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:161
     *
     * <h3>Variables</h3>
     * - <code>:limit</code> int, required in mysql.sql
     * - <code>:from</code> int, required in mysql.sql
     */
    public const CUCUMBER_GET_PUNISHMENTS_BANS_LIMITED = "cucumber.get.punishments.bans.limited";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:169
     */
    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_ALL = "cucumber.get.punishments.ip-bans.all";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:183
     */
    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_COUNT = "cucumber.get.punishments.ip-bans.count";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:173
     */
    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_CURRENT = "cucumber.get.punishments.ip-bans.current";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:180
     *
     * <h3>Variables</h3>
     * - <code>:limit</code> int, required in mysql.sql
     * - <code>:from</code> int, required in mysql.sql
     */
    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_LIMITED = "cucumber.get.punishments.ip-bans.limited";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:193
     */
    public const CUCUMBER_GET_PUNISHMENTS_MUTES_ALL = "cucumber.get.punishments.mutes.all";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:211
     */
    public const CUCUMBER_GET_PUNISHMENTS_MUTES_COUNT = "cucumber.get.punishments.mutes.count";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:199
     */
    public const CUCUMBER_GET_PUNISHMENTS_MUTES_CURRENT = "cucumber.get.punishments.mutes.current";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:208
     *
     * <h3>Variables</h3>
     * - <code>:limit</code> int, required in mysql.sql
     * - <code>:from</code> int, required in mysql.sql
     */
    public const CUCUMBER_GET_PUNISHMENTS_MUTES_LIMITED = "cucumber.get.punishments.mutes.limited";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:187
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
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:69
     *
     * <h3>Variables</h3>
     * - <code>:table</code> string, required in mysql.sql
     */
    public const CUCUMBER_MIGRATE_GET_COLUMNS_FROM_TABLE = "cucumber.migrate.get.columns-from-table";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:65
     */
    public const CUCUMBER_MIGRATE_GET_TABLES = "cucumber.migrate.get.tables";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:88
     */
    public const CUCUMBER_MIGRATE_TABLES_BANS_ALTER = "cucumber.migrate.tables.bans.alter";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:82
     */
    public const CUCUMBER_MIGRATE_TABLES_BANS_RENAME = "cucumber.migrate.tables.bans.rename";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:98
     */
    public const CUCUMBER_MIGRATE_TABLES_IP_BANS_ALTER = "cucumber.migrate.tables.ip-bans.alter";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:94
     */
    public const CUCUMBER_MIGRATE_TABLES_IP_BANS_RENAME = "cucumber.migrate.tables.ip-bans.rename";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:120
     */
    public const CUCUMBER_MIGRATE_TABLES_MUTES_ALTER = "cucumber.migrate.tables.mutes.alter";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:114
     */
    public const CUCUMBER_MIGRATE_TABLES_MUTES_RENAME = "cucumber.migrate.tables.mutes.rename";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:76
     */
    public const CUCUMBER_MIGRATE_TABLES_PLAYERS_RENAME = "cucumber.migrate.tables.players.rename";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:108
     */
    public const CUCUMBER_MIGRATE_TABLES_UBANS_ALTER = "cucumber.migrate.tables.ubans.alter";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:104
     */
    public const CUCUMBER_MIGRATE_TABLES_UBANS_RENAME = "cucumber.migrate.tables.ubans.rename";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:225
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
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:242
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
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:246
     *
     * <h3>Variables</h3>
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_IP_UNBAN = "cucumber.punish.ip-unban";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:263
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
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:253
     *
     * <h3>Variables</h3>
     * - <code>:moderator</code> string, required in mysql.sql
     * - <code>:reason</code> string, required in mysql.sql
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_UBAN = "cucumber.punish.uban";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:234
     *
     * <h3>Variables</h3>
     * - <code>:player</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_UNBAN = "cucumber.punish.unban";

    /**
     * <h4>Declared in:</h4>
     * - /home/alexis/pmmp/plugins/dev-cucumber/resources/mysql.sql:272
     *
     * <h3>Variables</h3>
     * - <code>:player</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_UNMUTE = "cucumber.punish.unmute";

}