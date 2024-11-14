<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241111204043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398BB01DC09');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398CFFE9AD6');
        $this->addSql('DROP INDEX IDX_F5299398BB01DC09 ON `order`');
        $this->addSql('DROP INDEX IDX_F5299398CFFE9AD6 ON `order`');
        $this->addSql('ALTER TABLE `order` DROP order_line_id, DROP orders_id, CHANGE total total NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE order_line ADD order_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE18D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_9CE58EE18D9F6D38 ON order_line (order_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE18D9F6D38');
        $this->addSql('DROP INDEX IDX_9CE58EE18D9F6D38 ON order_line');
        $this->addSql('ALTER TABLE order_line DROP order_id');
        $this->addSql('ALTER TABLE `order` ADD order_line_id INT NOT NULL, ADD orders_id INT DEFAULT NULL, CHANGE total total NUMERIC(10, 0) NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398BB01DC09 FOREIGN KEY (order_line_id) REFERENCES order_line (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F5299398BB01DC09 ON `order` (order_line_id)');
        $this->addSql('CREATE INDEX IDX_F5299398CFFE9AD6 ON `order` (orders_id)');
    }
}
