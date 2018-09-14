-- #!mysql
-- #{cucumber
-- #  {init
-- #    {players
CREATE TABLE IF NOT EXISTS players (
  id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(30) UNIQUE NOT NULL,
  ip VARCHAR(20) NOT NULL,
  first_join INT(11) UNSIGNED NOT NULL,
  last_join INT(11) UNSIGNED NOT NULL
);
-- #    }
-- #    {punishments
-- #      {bans
CREATE TABLE IF NOT EXISTS bans (
  id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  player_id INT(7) UNSIGNED UNIQUE NOT NULL,
  reason VARCHAR(500) DEFAULT NULL,
  expiration INT(11) NOT NULL,
  moderator VARCHAR(30) NOT NULL,
  FOREIGN KEY (player_id) REFERENCES players(id),
  FOREIGN KEY (moderator) REFERENCES players(name)
);
-- #      }
-- #      {ip-bans
CREATE TABLE IF NOT EXISTS ip_bans (
  id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip VARCHAR(20) UNIQUE NOT NULL,
  reason VARCHAR(500) DEFAULT NULL,
  expiration INT(11) NOT NULL,
  moderator VARCHAR(30) NOT NULL,
  FOREIGN KEY (moderator) REFERENCES players(name)
);
-- #      }
-- #      {ubans
CREATE TABLE IF NOT EXISTS ubans (
  id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip VARCHAR(20) UNIQUE NOT NULL,
  reason VARCHAR(500) DEFAULT NULL,
  moderator VARCHAR(30) NOT NULL,
  FOREIGN KEY (moderator) REFERENCES players(name)
);
-- #      }
-- #      {mutes
CREATE TABLE IF NOT EXISTS mutes (
  id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  player_id INT(7) UNSIGNED UNIQUE NOT NULL,
  reason VARCHAR(500) DEFAULT NULL,
  expiration INT(11) NOT NULL,
  moderator VARCHAR(30) NOT NULL,
  FOREIGN KEY (player_id) REFERENCES players(id),
  FOREIGN KEY (moderator) REFERENCES players(name)
);
-- #      }
-- #    }
-- #  }
-- #  {add
-- #    {player
-- #      :name string
-- #      :ip string
INSERT INTO players (name, ip, first_join, last_join)
VALUES (:name, :ip, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE ip = :ip, last_join = UNIX_TIMESTAMP();
-- #    }
-- #  }
-- #  {get
-- #    {player
-- #      {by-name
-- #        :name string
SELECT * FROM players WHERE name = :name;
-- #      }
-- #    }
-- #    {punishments
-- #      {bans
-- #        {all
SELECT bans.*, players.*, players.name AS player_name
FROM bans
INNER JOIN players ON bans.player_id = players.id;
-- #        }
-- #        {current
SELECT bans.*, players.*, players.name AS player_name
FROM bans
INNER JOIN players ON bans.player_id = players.id
WHERE bans.expiration > UNIX_TIMESTAMP();
-- #        }
-- #      }
-- #      {ip-bans
-- #        {all
SELECT * FROM ip_bans;
-- #        }
-- #        {current
SELECT * FROM ip_bans
WHERE expiration > UNIX_TIMESTAMP();
-- #        }
-- #      }
-- #      {ubans
SELECT * FROM ubans;
-- #      }
-- #      {mutes
-- #        {all
SELECT mutes.*, players.*, players.name AS player_name
FROM mutes
INNER JOIN players ON mutes.player_id = players.id;
-- #        }
-- #        {current
SELECT mutes.*, players.*, players.name AS player_name
FROM mutes
INNER JOIN players on mutes.player_id = players.id
WHERE mutes.expiration > UNIX_TIMESTAMP();
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
REPLACE INTO bans (player_id, reason, expiration, moderator)
SELECT id, :reason, :expiration, :moderator FROM players WHERE name = :player;
-- #    }
-- #    {unban
-- #      :player string
DELETE FROM bans
WHERE player_id IN (
  SELECT * FROM (
    SELECT id FROM players WHERE name = :player
  ) AS a
);
-- #    }
-- #    {ip-ban
-- #      :ip string
-- #      :reason string
-- #      :expiration int
-- #      :moderator string
REPLACE INTO ip_bans (ip, reason, expiration, moderator)
VALUES (:ip, :reason, :expiration, :moderator);
-- #    }
-- #    {ip-unban
-- #      :ip string
DELETE FROM ip_bans WHERE ip = :ip;
-- #    }
-- #    {uban
-- #      :ip string
-- #      :reason string
-- #      :moderator string
REPLACE INTO ubans (ip, reason, moderator)
VALUES (:ip, :reason, :moderator);
-- #    }
-- #    {mute
-- #      :player string
-- #      :reason string
-- #      :expiration int
-- #      :moderator string
REPLACE INTO mutes (player_id, reason, expiration, moderator)
SELECT id, :reason, :expiration, :moderator FROM players WHERE name = :player;
-- #    }
-- #    {unmute
-- #      :player string
DELETE FROM mutes
WHERE player_id IN (
  SELECT * FROM (
    SELECT id FROM players WHERE name = :player
  ) AS a
);
-- #    }
-- #  }
-- #}