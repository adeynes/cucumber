-- #!mysql
-- #{cucumber
-- #  {init
-- #    {players
CREATE TABLE IF NOT EXISTS cucumber_players (
    id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) UNIQUE NOT NULL,
    ip VARCHAR(20) NOT NULL,
    first_join INT(11) UNSIGNED NOT NULL,
    last_join INT(11) UNSIGNED NOT NULL
);
-- #    }
-- #    {punishments
-- #      {bans
CREATE TABLE IF NOT EXISTS cucumber_bans (
    id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id INT(7) UNSIGNED UNIQUE NOT NULL,
    reason VARCHAR(500) DEFAULT NULL,
    expiration INT UNSIGNED,
    moderator VARCHAR(30) NOT NULL,
    time_created INT UNSIGNED NOT NULL,
    FOREIGN KEY (player_id) REFERENCES cucumber_players(id),
    FOREIGN KEY (moderator) REFERENCES cucumber_players(name)
);
-- #      }
-- #      {ip-bans
CREATE TABLE IF NOT EXISTS cucumber_ip_bans (
    id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(20) UNIQUE NOT NULL,
    reason VARCHAR(500) DEFAULT NULL,
    expiration INT UNSIGNED,
    moderator VARCHAR(30) NOT NULL,
    time_created INT UNSIGNED NOT NULL,
    FOREIGN KEY (moderator) REFERENCES cucumber_players(name)
);
-- #      }
-- #      {ubans
CREATE TABLE IF NOT EXISTS cucumber_ubans (
    id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(20) UNIQUE NOT NULL,
    reason VARCHAR(500) DEFAULT NULL,
    moderator VARCHAR(30) NOT NULL,
    time_created INT UNSIGNED NOT NULL,
    FOREIGN KEY (moderator) REFERENCES cucumber_players(name)
);
-- #      }
-- #      {mutes
CREATE TABLE IF NOT EXISTS cucumber_mutes (
    id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id INT(7) UNSIGNED UNIQUE NOT NULL,
    reason VARCHAR(500) DEFAULT NULL,
    expiration INT UNSIGNED,
    moderator VARCHAR(30) NOT NULL,
    time_created INT UNSIGNED NOT NULL,
    FOREIGN KEY (player_id) REFERENCES cucumber_players(id),
    FOREIGN KEY (moderator) REFERENCES cucumber_players(name)
);
-- #      }
-- #    }
-- #  }
-- #  {migrate
-- #    {get
-- #      {tables
SHOW TABLES;
-- #      }
-- #      {columns-from-table
-- #        :table string
SHOW COLUMNS FROM :table;
-- #      }
-- #    }
-- #    {tables
-- #      {players
-- #        {rename
# noinspection SqlResolve
RENAME TABLE players TO cucumber_players;
-- #        }
-- #      }
-- #      {bans
-- #        {rename
# noinspection SqlResolve
RENAME TABLE bans TO cucumber_bans;
-- #        }
-- #        {alter
# noinspection SqlResolve
ALTER TABLE cucumber_bans
    CHANGE COLUMN player player_id INT(7) UNSIGNED NOT NULL,
    MODIFY COLUMN expiration INT UNSIGNED,
    ADD COLUMN time_created INT UNSIGNED NOT NULL AFTER moderator;
-- #        }
-- #      }
-- #      {ip-bans
-- #        {rename
# noinspection SqlResolve
RENAME TABLE ip_bans TO cucumber_ip_bans;
-- #        }
-- #        {alter
ALTER TABLE cucumber_ip_bans
    MODIFY COLUMN expiration INT UNSIGNED,
    ADD COLUMN time_created INT UNSIGNED NOT NULL AFTER moderator;
-- #        }
-- #      }
-- #      {ubans
-- #        {rename
# noinspection SqlResolve
RENAME TABLE ubans TO cucumber_ubans;
-- #        }
-- #        {alter
ALTER TABLE cucumber_ubans
    ADD COLUMN time_created INT UNSIGNED NOT NULL AFTER moderator;
-- #        }
-- #      }
-- #      {mutes
-- #        {rename
# noinspection SqlResolve
RENAME TABLE mutes TO cucumber_mutes;
-- #        }
-- #        {alter
# noinspection SqlResolve
ALTER TABLE cucumber_mutes
    CHANGE COLUMN player player_id INT(7) UNSIGNED NOT NULL,
    MODIFY COLUMN expiration INT UNSIGNED,
    ADD COLUMN time_created INT UNSIGNED NOT NULL AFTER moderator;
-- #        }
-- #      }
-- #    }
-- #  }
-- #  {add
-- #    {player
-- #      :name string
-- #      :ip string
INSERT INTO cucumber_players (name, ip, first_join, last_join)
VALUES (:name, :ip, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE ip = :ip, last_join = UNIX_TIMESTAMP();
-- #    }
-- #  }
-- #  {get
-- #    {player
-- #      {by-name
-- #        :name string
SELECT * FROM cucumber_players WHERE name = :name;
-- #      }
-- #    }
-- #    {punishments
-- #      {bans
-- #        {all
SELECT cucumber_bans.*, cucumber_players.*, cucumber_players.name AS player_name
FROM cucumber_bans
INNER JOIN cucumber_players ON cucumber_bans.player_id = cucumber_players.id;
-- #        }
-- #        {current
SELECT cucumber_bans.*, cucumber_players.*, cucumber_players.name AS player_name
FROM cucumber_bans
INNER JOIN cucumber_players ON cucumber_bans.player_id = cucumber_players.id
WHERE cucumber_bans.expiration > UNIX_TIMESTAMP();
-- #        }
-- #        {limited
-- #          :from int
-- #          :limit int
SELECT cucumber_bans.*, cucumber_players.*, cucumber_players.name AS player_name
FROM cucumber_bans
INNER JOIN cucumber_players ON cucumber_bans.player_id = cucumber_players.id
ORDER BY cucumber_bans.time_created DESC
LIMIT :from, :limit;
-- #        }
-- #        {count
SELECT COUNT(*) AS count FROM cucumber_bans;
-- #        }
-- #        {by-player
-- #          :player string
SELECT cucumber_bans.*, cucumber_players.*, cucumber_players.name AS player_name
FROM cucumber_bans
INNER JOIN cucumber_players ON cucumber_bans.player_id = cucumber_players.id
WHERE cucumber_players.name = :player;
-- #        }
-- #      }
-- #      {ip-bans
-- #        {all
SELECT * FROM cucumber_ip_bans;
-- #        }
-- #        {current
SELECT * FROM cucumber_ip_bans
WHERE expiration > UNIX_TIMESTAMP();
-- #        }
-- #        {limited
-- #          :from int
-- #          :limit int
SELECT * FROM cucumber_ip_bans
ORDER BY time_created DESC
LIMIT :from, :limit;
-- #        }
-- #        {count
SELECT COUNT(*) AS count FROM cucumber_ip_bans;
-- #        }
-- #        {by-ip
-- #          :ip string
SELECT * FROM cucumber_ip_bans
WHERE ip = :ip;
-- #        }
-- #      }
-- #      {ubans
SELECT * FROM cucumber_ubans;
-- #      }
-- #      {mutes
-- #        {all
SELECT cucumber_mutes.*, cucumber_players.*, cucumber_players.name AS player_name
FROM cucumber_mutes
INNER JOIN cucumber_players ON cucumber_mutes.player_id = cucumber_players.id;
-- #        }
-- #        {current
SELECT cucumber_mutes.*, cucumber_players.*, cucumber_players.name AS player_name
FROM cucumber_mutes
INNER JOIN cucumber_players ON cucumber_mutes.player_id = cucumber_players.id
WHERE cucumber_mutes.expiration > UNIX_TIMESTAMP();
-- #        }
-- #        {limited
-- #          :from int
-- #          :limit int
SELECT cucumber_mutes.*, cucumber_players.*, cucumber_players.name AS player_name
FROM cucumber_mutes
INNER JOIN cucumber_players ON cucumber_mutes.player_id = cucumber_players.id
ORDER BY cucumber_mutes.time_created DESC
LIMIT :from, :limit;
-- #        }
-- #        {count
SELECT COUNT(*) AS count FROM cucumber_mutes;
-- #        }
-- #        {by-player
-- #          :player string
SELECT cucumber_mutes.*, cucumber_players.*, cucumber_players.name AS player_name
FROM cucumber_mutes
INNER JOIN cucumber_players ON cucumber_mutes.player_id = cucumber_players.id
WHERE cucumber_players.name = :player;
-- #        }
-- #      }
-- #    }
-- #  }
-- #  {punish
-- #    {ban
-- #      :player string
-- #      :reason string
-- #      :expiration int
-- #      :moderator string
REPLACE INTO cucumber_bans (player_id, reason, expiration, moderator, time_created)
    SELECT id, :reason, :expiration, :moderator, UNIX_TIMESTAMP()
    FROM cucumber_players
    WHERE name = :player;
-- #    }
-- #    {unban
-- #      :player string
DELETE FROM cucumber_bans
WHERE player_id IN (
    SELECT * FROM (
        SELECT id FROM cucumber_players WHERE name = :player
    ) AS a
);
-- #    }
-- #    {ip-ban
-- #      :ip string
-- #      :reason string
-- #      :expiration int
-- #      :moderator string
REPLACE INTO cucumber_ip_bans (ip, reason, expiration, moderator, time_created)
VALUES (:ip, :reason, :expiration, :moderator, UNIX_TIMESTAMP());
-- #    }
-- #    {ip-unban
-- #      :ip string
DELETE FROM cucumber_ip_bans WHERE ip = :ip;
-- #    }
-- #    {uban
-- #      :ip string
-- #      :reason string
-- #      :moderator string
REPLACE INTO cucumber_ubans (ip, reason, moderator)
VALUES (:ip, :reason, :moderator);
-- #    }
-- #    {mute
-- #      :player string
-- #      :reason string
-- #      :expiration int
-- #      :moderator string
REPLACE INTO cucumber_mutes (player_id, reason, expiration, moderator, time_created)
    SELECT id, :reason, :expiration, :moderator, UNIX_TIMESTAMP()
    FROM cucumber_players
    WHERE name = :player;
-- #    }
-- #    {unmute
-- #      :player string
DELETE FROM cucumber_mutes
WHERE player_id IN (
    SELECT * FROM (
        SELECT id FROM cucumber_players WHERE name = :player
    ) AS a
);
-- #    }
-- #  }
-- #}