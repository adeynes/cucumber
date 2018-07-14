-- #!mysql
-- #{cucumber
-- #  {init
-- #    {players
CREATE TABLE IF NOT EXISTS players (
  id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(30) UNIQUE NOT NULL,
  ip VARCHAR(20) NOT NULL,
  uid VARCHAR(255) UNIQUE NOT NULL,
  first_join INT(11) UNSIGNED NOT NULL,
  last_join INT(11) UNSIGNED NOT NULL
);
-- #    }
-- #    {punishments
-- #      {bans
CREATE TABLE IF NOT EXISTS bans (
  id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uid VARCHAR(255) UNIQUE NOT NULL,
  reason VARCHAR(500) UNIQUE DEFAULT '',
  expiration INT(11) NOT NULL,
  moderator INT(7) NOT NULL,
  FOREIGN KEY (uid) REFERENCES players(uid),
  FOREIGN KEY (moderator) REFERENCES players(id)
);
-- #      }
-- #      {ip-bans
CREATE TABLE IF NOT EXISTS ip_bans (
  id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip VARCHAR(20) UNIQUE NOT NULL,
  reason VARCHAR(511) UNIQUE DEFAULT '',
  expiration INT(11) NOT NULL,
  moderator INT(7) NOT NULL,
  FOREIGN KEY (ip) REFERENCES players(ip),
  FOREIGN KEY (moderator) REFERENCES players(id)
);
-- #      }
-- #      {mutes
CREATE TABLE IF NOT EXISTS mutes (
  id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uid VARCHAR(255) UNIQUE NOT NULL,
  reason VARCHAR(500) UNIQUE DEFAULT '',
  expiration INT(11) NOT NULL,
  moderator INT(7) NOT NULL,
  FOREIGN KEY (uid) REFERENCES players(uid),
  FOREIGN KEY (moderator) REFERENCES players(id)
);
-- #      }
-- #    }
-- #  }
-- #  {get
-- #    {find
-- #      {player
-- #        {by-name
-- #          :name string
SELECT id, ip, uid FROM players WHERE name = :name;
-- #        }
-- #      }
-- #    }
-- #    {punishments
-- #      {bans
SELECT id, uid, reason, expiration, moderator FROM bans;
-- #      }
-- #      {ip-bans
SELECT id, ip, reason, expiration, moderator FROM ip_bans;
-- #      }
-- #      {mutes
SELECT id, uid, reason, expiration, moderator FROM mutes;
-- #      }
-- #    }
-- #  }
-- #  {punish
-- #    {ban
-- #      :uid string
-- #      :reason string
-- #      :expiration int
-- #      :moderator int
INSERT INTO bans (uid, reason, expiration, moderator)
VALUES (:uid, :reason, :expiration, :moderator);
-- #    }
-- #    {unban
-- #      :uid string
DELETE FROM bans WHERE uid = :uid;
-- #    }
-- #    {ip-ban
-- #      :ip string
-- #      :reason string
-- #      :expiration int
-- #      :moderator int
INSERT INTO ip_bans (ip, reason, expiration, moderator)
VALUES (:ip, :reason, :expiration, :moderator);
-- #    }
-- #    {ip-unban
-- #      :ip string
DELETE FROM ip_bans WHERE ip = :ip;
-- #    }
-- #    {mute
-- #      :uid string
-- #      :reason string
-- #      :expiration int
-- #      :moderator int
INSERT INTO mutes (uid, reason, expiration, moderator)
VALUES (:uid, :reason, :expiration, :moderator);
-- #    }
-- #    {unmute
-- #      :uid string
DELETE FROM mutes WHERE uid = :uid;
-- #    }
-- #  }
-- #}