-- #!mysql
-- #{cucumber
-- #  {init
-- #    {players
CREATE TABLE IF NOT EXISTS players (
  name VARCHAR(30) NOT NULL,
  ip VARCHAR(20) NOT NULL,
  uid VARCHAR(255) PRIMARY KEY
);
-- #    }
-- #    {punishments
-- #      {ban
CREATE TABLE IF NOT EXISTS bans (
  uid VARCHAR(255) PRIMARY KEY,
  reason VARCHAR(511),
  expiration VARCHAR(11) NOT NULL
);
-- #      }
-- #      {ip_ban
CREATE TABLE IF NOT EXISTS ip_bans (
  ip VARCHAR(20) PRIMARY KEY,
  reason VARCHAR(511),
  expiration VARCHAR(11) NOT NULL
);
-- #      }
-- #      {mute
CREATE TABLE IF NOT EXISTS mutes (
  uid VARCHAR(255) PRIMARY KEY,
  reason VARCHAR(511),
  expiration VARCHAR(11) NOT NULL
);
-- #      }
-- #    }
-- #  }
-- #}