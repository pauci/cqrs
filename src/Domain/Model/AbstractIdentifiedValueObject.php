<?php

namespace CQRS\Domain\Model;

/**
 * This class serves as a ValueObject super-type to use when we need to treat Value type in the domain model as an
 * entity in the data model. Such need emerges for example when we need to persist a collection of Value instances using
 * ORM. It tucks away the necessary surrogate identity (primary key) out of sight of actual domain model implementation.
 *
 * [Implementing Domain-Driven Design p. 255]
 */
abstract class AbstractIdentifiedValueObject extends AbstractIdentifiedDomainObject implements ValueObjectInterface
{

}
