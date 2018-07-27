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
  player INT(7) UNSIGNED UNIQUE NOT NULL,
  reason VARCHAR(500) DEFAULT NULL,
  expiration INT(11) NOT NULL,
  moderator VARCHAR(30) NOT NULL,
  FOREIGN KEY (player) REFERENCES players(id),
  FOREIGN KEY (moderator) REFERENCES players(name)
);
-- #      }
-- #      {ip-bans
CREATE TABLE IF NOT EXISTS ip_bans (
  id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip VARCHAR(20) UNIQUE NOT NULL,
  reason VARCHAR(511) DEFAULT NULL,
  expiration INT(11) NOT NULL,
  moderator VARCHAR(30) NOT NULL,
  FOREIGN KEY (moderator) REFERENCES players(name)
);
-- #      }
-- #      {mutes
CREATE TABLE IF NOT EXISTS mutes (
  id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  player INT(7) UNSIGNED UNIQUE NOT NULL,
  reason VARCHAR(500) DEFAULT NULL,
  expiration INT(11) NOT NULL,
  moderator VARCHAR(30) NOT NULL,
  FOREIGN KEY (player) REFERENCES players(id),
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
SELECT bans.*, players.*
FROM bans
INNER JOIN players ON bans.player = players.id;
-- #      }
-- #      {ip-bans
SELECT * FROM ip_bans;
-- #      }
-- #      {mutes
SELECT mutes.*, players.*
FROM mutes
INNER JOIN players ON mutes.player = players.id;
-- #      }
-- #    }
-- #  }
-- #  {punish
-- #    {ban
-- #      :name string
-- #      :reason string
-- #      :expiration int
-- #      :moderator string
REPLACE INTO bans (player, reason, expiration, moderator)
SELECT id, :reason, :expiration, :moderator FROM players WHERE name = :name;
-- #    }
-- #    {unban
-- #      :name string
DELETE FROM bans
WHERE player IN (
  SELECT * FROM (
    SELECT id FROM players WHERE name = :name
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
-- #    {mute
-- #      :name string
-- #      :reason string
-- #      :expiration int
-- #      :moderator string
REPLACE INTO mutes (player, reason, expiration, moderator)
SELECT id, :reason, :expiration, :moderator FROM players WHERE name = :name;
-- #    }
-- #    {unmute
-- #      :name string
DELETE FROM mutes
WHERE player IN (
  SELECT * FROM (
    SELECT id FROM players WHERE name = :name
  ) AS a
);
-- #    }
-- #  }
-- #}