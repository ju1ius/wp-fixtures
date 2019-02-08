<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AbstractRepository.php
 *
 * LICENSE: This source file is created by the company around Mike Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://mike-pretzlaw.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@mike-pretzlaw.de so we can mail you a copy.
 *
 * @package    pretzlaw/wp-fixtures
 * @copyright  2019 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/pretzlaw/wp-fixtures
 * @since      2019-02-03
 */

declare(strict_types=1);

namespace Pretzlaw\WordPress\Fixtures\Repository;

use Pretzlaw\WordPress\Fixtures\Entity\Sanitizable;
use Pretzlaw\WordPress\Fixtures\Entity\Validatable;

/**
 * AbstractRepository
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @param $object
     */
    public function persist($object)
    {
        $sanitized = $this->parse($object);

        $id = $this->find($sanitized);
        if ($id !== null) {
            $object->ID = $id;
            $sanitized->ID = $id;
            $this->update($sanitized);

            return;
        }

        $id = $this->create($sanitized);

        if ($id instanceof \WP_Error) {
            throw new PersistException($object, $id);
        }

        $object->ID = $id;
    }

    protected function toArray($source)
    {
        return get_object_vars($source);
    }

    /**
     * @param $object
     * @return Sanitizable|\stdClass
     */
    protected function parse($object)
    {
        $double = clone $object;

        if ($double instanceof Sanitizable) {
            $double->sanitize();
        }

        if ($double instanceof Validatable) {
            $double->validate();
        }

        return $double;
    }

    abstract protected function create($object): int;

    abstract protected function update($object);
}