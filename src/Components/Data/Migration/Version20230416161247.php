<?php

declare(strict_types=1);

namespace App\Components\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230416161247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE friendship_requests (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, friend_id INT NOT NULL, created_at INT NOT NULL, is_refused TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE friendships (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, friend_id INT NOT NULL, created_at INT NOT NULL, INDEX IDX_USER_ID (user_id), INDEX IDX_FRIEND_ID (friend_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE friendship_requests');
        $this->addSql('DROP TABLE friendships');
    }
}
