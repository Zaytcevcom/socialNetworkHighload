<?php

declare(strict_types=1);

namespace App\Components\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308074008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oauth_auth_codes (identifier VARCHAR(80) NOT NULL, expiry_date_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', user_identifier CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_refresh_tokens (identifier VARCHAR(80) NOT NULL, expiry_date_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', user_identifier INT NOT NULL, locale VARCHAR(255) DEFAULT NULL, created_at INT DEFAULT 0 NOT NULL, INDEX IDX_SEARCH (identifier), INDEX IDX_USER_ID (user_identifier), PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT UNSIGNED AUTO_INCREMENT NOT NULL, username VARCHAR(100) NOT NULL, first_name VARCHAR(50) NOT NULL, second_name VARCHAR(50) NOT NULL, sex INT DEFAULT NULL, birthdate DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', biography VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE oauth_auth_codes');
        $this->addSql('DROP TABLE oauth_refresh_tokens');
        $this->addSql('DROP TABLE users');
    }
}
