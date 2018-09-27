<?php
declare(strict_types=1);

namespace adeynes\cucumber\utils;

interface Queries
{

    /**
     * <h3>Variables</h3>
     * - <code>:name</code> string, required in mysql.sql
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_ADD_PLAYER = "cucumber.add.player";

    /**
     * <h3>Variables</h3>
     * - <code>:name</code> string, required in mysql.sql
     */
    public const CUCUMBER_GET_PLAYER_BY_NAME = "cucumber.get.player.by-name";

    public const CUCUMBER_GET_PUNISHMENTS_BANS_ALL = "cucumber.get.punishments.bans.all";

    public const CUCUMBER_GET_PUNISHMENTS_BANS_COUNT = "cucumber.get.punishments.bans.count";

    public const CUCUMBER_GET_PUNISHMENTS_BANS_CURRENT = "cucumber.get.punishments.bans.current";

    /**
     * <h3>Variables</h3>
     * - <code>:limit</code> int, required in mysql.sql
     * - <code>:from</code> int, required in mysql.sql
     */
    public const CUCUMBER_GET_PUNISHMENTS_BANS_LIMITED = "cucumber.get.punishments.bans.limited";

    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_ALL = "cucumber.get.punishments.ip-bans.all";

    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_COUNT = "cucumber.get.punishments.ip-bans.count";

    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_CURRENT = "cucumber.get.punishments.ip-bans.current";

    /**
     * <h3>Variables</h3>
     * - <code>:limit</code> int, required in mysql.sql
     * - <code>:from</code> int, required in mysql.sql
     */
    public const CUCUMBER_GET_PUNISHMENTS_IP_BANS_LIMITED = "cucumber.get.punishments.ip-bans.limited";

    public const CUCUMBER_GET_PUNISHMENTS_MUTES_ALL = "cucumber.get.punishments.mutes.all";

    public const CUCUMBER_GET_PUNISHMENTS_MUTES_COUNT = "cucumber.get.punishments.mutes.count";

    public const CUCUMBER_GET_PUNISHMENTS_MUTES_CURRENT = "cucumber.get.punishments.mutes.current";

    /**
     * <h3>Variables</h3>
     * - <code>:limit</code> int, required in mysql.sql
     * - <code>:from</code> int, required in mysql.sql
     */
    public const CUCUMBER_GET_PUNISHMENTS_MUTES_LIMITED = "cucumber.get.punishments.mutes.limited";

    public const CUCUMBER_GET_PUNISHMENTS_UBANS = "cucumber.get.punishments.ubans";

    /**
     * <h3>Variables</h3>
     * - <code>:expiration</code> int, required in mysql.sql
     * - <code>:moderator</code> string, required in mysql.sql
     * - <code>:player</code> string, required in mysql.sql
     * - <code>:reason</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_BAN = "cucumber.punish.ban";

    /**
     * <h3>Variables</h3>
     * - <code>:expiration</code> int, required in mysql.sql
     * - <code>:moderator</code> string, required in mysql.sql
     * - <code>:reason</code> string, required in mysql.sql
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_IP_BAN = "cucumber.punish.ip-ban";

    /**
     * <h3>Variables</h3>
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_IP_UNBAN = "cucumber.punish.ip-unban";

    /**
     * <h3>Variables</h3>
     * - <code>:expiration</code> int, required in mysql.sql
     * - <code>:moderator</code> string, required in mysql.sql
     * - <code>:player</code> string, required in mysql.sql
     * - <code>:reason</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_MUTE = "cucumber.punish.mute";

    /**
     * <h3>Variables</h3>
     * - <code>:moderator</code> string, required in mysql.sql
     * - <code>:reason</code> string, required in mysql.sql
     * - <code>:ip</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_UBAN = "cucumber.punish.uban";

    /**
     * <h3>Variables</h3>
     * - <code>:player</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_UNBAN = "cucumber.punish.unban";

    /**
     * <h3>Variables</h3>
     * - <code>:player</code> string, required in mysql.sql
     */
    public const CUCUMBER_PUNISH_UNMUTE = "cucumber.punish.unmute";

}