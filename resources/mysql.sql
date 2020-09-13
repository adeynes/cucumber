-- #!mysql
-- #{cucumber
-- #  {init
-- #    {players
CREATE TABLE IF NOT EXISTS cucumber_players (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(32) UNIQUE NOT NULL,
    ip VARCHAR(39) NOT NULL,
    first_join INT UNSIGNED NOT NULL,
    last_join INT UNSIGNED NOT NULL
);
-- #    }
-- #    {punishments
-- #      {bans
CREATE TABLE IF NOT EXISTS cucumber_bans (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id INT UNSIGNED NOT NULL,
    reason VARCHAR(500) DEFAULT NULL,
    expiration INT UNSIGNED,
    moderator VARCHAR(32) NOT NULL,
    time_created INT UNSIGNED NOT NULL,
    FOREIGN KEY (player_id) REFERENCES cucumber_players(id),
    FOREIGN KEY (moderator) REFERENCES cucumber_players(name)
);
-- #      }
-- #      {ip-bans
CREATE TABLE IF NOT EXISTS cucumber_ip_bans (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(39) NOT NULL,
    reason VARCHAR(500) DEFAULT NULL,
    expiration INT UNSIGNED,
    moderator VARCHAR(32) NOT NULL,
    time_created INT UNSIGNED NOT NULL,
    FOREIGN KEY (moderator) REFERENCES cucumber_players(name)
);
-- #      }
-- #      {ubans
CREATE TABLE IF NOT EXISTS cucumber_ubans (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(39) UNIQUE NOT NULL,
    reason VARCHAR(500) DEFAULT NULL,
    moderator VARCHAR(32) NOT NULL,
    time_created INT UNSIGNED NOT NULL,
    FOREIGN KEY (moderator) REFERENCES cucumber_players(name)
);
-- #      }
-- #      {mutes
CREATE TABLE IF NOT EXISTS cucumber_mutes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id INT UNSIGNED NOT NULL,
    reason VARCHAR(500) DEFAULT NULL,
    expiration INT UNSIGNED,
    moderator VARCHAR(32) NOT NULL,
    time_created INT UNSIGNED NOT NULL,
    FOREIGN KEY (player_id) REFERENCES cucumber_players(id),
    FOREIGN KEY (moderator) REFERENCES cucumber_players(name)
);
-- #      }
-- #      {warnings
CREATE TABLE IF NOT EXISTS cucumber_warnings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id INT UNSIGNED NOT NULL,
    reason VARCHAR(500) DEFAULT NULL,
    expiration INT UNSIGNED,
    moderator VARCHAR(32) NOT NULL,
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
-- #        {alter-modify
ALTER TABLE cucumber_players
    MODIFY COLUMN id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    MODIFY COLUMN name VARCHAR(32) UNIQUE NOT NULL,
    MODIFY COLUMN ip VARCHAR(39) NOT NULL,
    MODIFY COLUMN first_join INT UNSIGNED NOT NULL,
    MODIFY COLUMN last_join INT UNSIGNED NOT NULL;
-- #        }
-- #      }
-- #      {bans
-- #        {rename
# noinspection SqlResolve
RENAME TABLE bans TO cucumber_bans;
-- #        }
-- #        {alter-change
# noinspection SqlResolve
ALTER TABLE cucumber_bans
    CHANGE COLUMN player player_id INT UNSIGNED NOT NULL,
    ADD COLUMN time_created INT UNSIGNED NOT NULL AFTER moderator;
-- #        }
-- #        {alter-modify
ALTER TABLE cucumber_bans
    MODIFY COLUMN id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    MODIFY COLUMN expiration INT UNSIGNED,
    MODIFY COLUMN moderator VARCHAR(32) NOT NULL;
-- #        }
-- #      }
-- #      {ip-bans
-- #        {rename
# noinspection SqlResolve
RENAME TABLE ip_bans TO cucumber_ip_bans;
-- #        }
-- #        {alter-change
ALTER TABLE cucumber_ip_bans
    ADD COLUMN time_created INT UNSIGNED NOT NULL AFTER moderator;
-- #        }
-- #        {alter-modify
ALTER TABLE cucumber_ip_bans
    MODIFY COLUMN id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    MODIFY COLUMN ip VARCHAR(39) NOT NULL,
    MODIFY COLUMN expiration INT UNSIGNED,
    MODIFY COLUMN moderator VARCHAR(32) NOT NULL;
-- #        }
-- #      }
-- #      {ubans
-- #        {rename
# noinspection SqlResolve
RENAME TABLE ubans TO cucumber_ubans;
-- #        }
-- #        {alter-change
ALTER TABLE cucumber_ubans
    ADD COLUMN time_created INT UNSIGNED NOT NULL AFTER moderator;
-- #        }
-- #        {alter-modify
ALTER TABLE cucumber_ubans
    MODIFY COLUMN id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    MODIFY COLUMN ip VARCHAR(39) UNIQUE NOT NULL,
    MODIFY COLUMN moderator VARCHAR(32) NOT NULL;
-- #        }
-- #      }
-- #      {mutes
-- #        {rename
# noinspection SqlResolve
RENAME TABLE mutes TO cucumber_mutes;
-- #        }
-- #        {alter-change
# noinspection SqlResolve
ALTER TABLE cucumber_mutes
    CHANGE COLUMN player player_id INT UNSIGNED NOT NULL,
    ADD COLUMN time_created INT UNSIGNED NOT NULL AFTER moderator;
-- #        }
-- #        {alter-modify
ALTER TABLE cucumber_mutes
    MODIFY COLUMN id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    MODIFY COLUMN expiration INT UNSIGNED,
    MODIFY COLUMN moderator VARCHAR(32) NOT NULL;
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
-- #      {by-ip
-- #        :ip string
SELECT * FROM cucumber_players WHERE ip = :ip;
-- #      }
-- #    }
-- #    {punishments
-- #      {bans
-- #        {all
SELECT cucumber_bans.*,
       cucumber_players.*,
       cucumber_bans.id AS ban_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
FROM cucumber_bans
INNER JOIN cucumber_players ON cucumber_bans.player_id = cucumber_players.id;
-- #        }
-- #        {current
SELECT cucumber_bans.*,
       cucumber_players.*,
       cucumber_bans.id AS ban_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
FROM cucumber_bans
INNER JOIN cucumber_players ON cucumber_bans.player_id = cucumber_players.id
WHERE cucumber_bans.expiration > UNIX_TIMESTAMP() OR cucumber_bans.expiration IS NULL;
-- #        }
-- #        {limited
-- #          :from int
-- #          :limit int
-- #          :all bool false
SELECT cucumber_bans.*,
       cucumber_players.*,
       cucumber_bans.id AS ban_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
FROM cucumber_bans
INNER JOIN cucumber_players ON cucumber_bans.player_id = cucumber_players.id
WHERE cucumber_bans.expiration > UNIX_TIMESTAMP() OR cucumber_bans.expiration IS NULL OR :all
ORDER BY cucumber_bans.time_created DESC
LIMIT :from, :limit;
-- #        }
-- #        {count
SELECT COUNT(*) AS count FROM cucumber_bans;
-- #        }
-- #        {by-player
-- #          :player string
SELECT cucumber_bans.*,
       cucumber_players.*,
       cucumber_bans.id AS ban_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
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
WHERE expiration > UNIX_TIMESTAMP() OR expiration IS NULL;
-- #        }
-- #        {limited
-- #          :from int
-- #          :limit int
-- #          :all bool false
SELECT * FROM cucumber_ip_bans
WHERE expiration > UNIX_TIMESTAMP() OR expiration IS NULL OR :all
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
SELECT cucumber_mutes.*,
       cucumber_players.*,
       cucumber_mutes.id AS mute_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
FROM cucumber_mutes
INNER JOIN cucumber_players ON cucumber_mutes.player_id = cucumber_players.id;
-- #        }
-- #        {current
SELECT cucumber_mutes.*,
       cucumber_players.*,
       cucumber_mutes.id AS mute_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
FROM cucumber_mutes
INNER JOIN cucumber_players ON cucumber_mutes.player_id = cucumber_players.id
WHERE cucumber_mutes.expiration > UNIX_TIMESTAMP() OR cucumber_mutes.expiration IS NULL;
-- #        }
-- #        {limited
-- #          :from int
-- #          :limit int
-- #          :all bool false
SELECT cucumber_mutes.*,
       cucumber_players.*,
       cucumber_mutes.id AS mute_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
FROM cucumber_mutes
INNER JOIN cucumber_players ON cucumber_mutes.player_id = cucumber_players.id
WHERE cucumber_mutes.expiration > UNIX_TIMESTAMP() OR cucumber_mutes.expiration IS NULL OR :all
ORDER BY cucumber_mutes.time_created DESC
LIMIT :from, :limit;
-- #        }
-- #        {count
SELECT COUNT(*) AS count FROM cucumber_mutes;
-- #        }
-- #        {by-player
-- #          :player string
SELECT cucumber_mutes.*,
       cucumber_players.*,
       cucumber_mutes.id AS mute_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
FROM cucumber_mutes
INNER JOIN cucumber_players ON cucumber_mutes.player_id = cucumber_players.id
WHERE cucumber_players.name = :player;
-- #        }
-- #      }
-- #      {warnings
-- #        {all
SELECT cucumber_warnings.*,
       cucumber_players.*,
       cucumber_warnings.id AS warning_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
FROM cucumber_warnings
INNER JOIN cucumber_players ON cucumber_warnings.player_id = cucumber_players.id;
-- #        }
-- #        {current
SELECT cucumber_warnings.*,
       cucumber_players.*,
       cucumber_warnings.id AS warning_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
FROM cucumber_warnings
INNER JOIN cucumber_players ON cucumber_warnings.player_id = cucumber_players.id
WHERE cucumber_warnings.expiration > UNIX_TIMESTAMP() OR cucumber_warnings.expiration IS NULL;
-- #        }
-- #        {limited
-- #          :from int
-- #          :limit int
-- #          :all bool false
SELECT cucumber_warnings.*,
       cucumber_players.*,
       cucumber_warnings.id AS warning_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
FROM cucumber_warnings
INNER JOIN cucumber_players ON cucumber_warnings.player_id = cucumber_players.id
WHERE cucumber_warnings.expiration > UNIX_TIMESTAMP() Or cucumber_warnings.expiration IS NULL OR :all
ORDER BY cucumber_warnings.time_created DESC
LIMIT :from, :limit;
-- #        }
-- #        {count
SELECT COUNT(*) AS count FROM cucumber_warnings;
-- #        }
-- #        {by-id
-- #          :id int
SELECT cucumber_warnings.*,
       cucumber_players.*,
       cucumber_warnings.id AS warning_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
FROM cucumber_warnings
INNER JOIN cucumber_players ON cucumber_warnings.player_id = cucumber_players.id
WHERE cucumber_warnings.id = :id;
-- #        }
-- #        {by-player
-- #          :player string
-- #          :all bool false
SELECT cucumber_warnings.*,
       cucumber_players.*,
       cucumber_warnings.id AS warning_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
FROM cucumber_warnings
INNER JOIN cucumber_players ON cucumber_warnings.player_id = cucumber_players.id
WHERE
    (cucumber_players.name = :player)
    AND
    (cucumber_warnings.expiration > UNIX_TIMESTAMP() OR cucumber_warnings.expiration IS NULL OR :all)
ORDER BY cucumber_warnings.time_created DESC;
-- #        }
-- #        {latest
-- #          :player string
-- #          :moderator string
SELECT cucumber_warnings.*,
       cucumber_players.*,
       cucumber_warnings.id AS warning_id,
       cucumber_players.id AS player_id,
       cucumber_players.name AS player_name
FROM cucumber_warnings
INNER JOIN cucumber_players ON cucumber_warnings.player_id = cucumber_players.id
WHERE cucumber_players.name = :player AND cucumber_warnings.moderator = :moderator
ORDER BY cucumber_warnings.time_created DESC
LIMIT 0, 1;
-- #        }
-- #      }
-- #    }
-- #  }
-- #  {punish
-- #    {ban
-- #      :player string
-- #      :reason string
-- #      :expiration ?int
-- #      :moderator string
INSERT INTO cucumber_bans (player_id, reason, expiration, moderator, time_created)
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
-- #      :expiration ?int
-- #      :moderator string
INSERT INTO cucumber_ip_bans (ip, reason, expiration, moderator, time_created)
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
REPLACE INTO cucumber_ubans (ip, reason, moderator, time_created)
VALUES (:ip, :reason, :moderator, UNIX_TIMESTAMP());
-- #    }
-- #    {mute
-- #      :player string
-- #      :reason string
-- #      :expiration ?int
-- #      :moderator string
INSERT INTO cucumber_mutes (player_id, reason, expiration, moderator, time_created)
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
-- #    {warn
-- #      :player string
-- #      :reason string
-- #      :expiration ?int
-- #      :moderator string
INSERT INTO cucumber_warnings (player_id, reason, expiration, moderator, time_created)
    SELECT id, :reason, :expiration, :moderator, UNIX_TIMESTAMP()
    FROM cucumber_players
    WHERE name = :player;
-- #    }
-- #    {delwarn
-- #      :id int
DELETE FROM cucumber_warnings WHERE id = :id;
-- #    }
-- #  }
-- #}