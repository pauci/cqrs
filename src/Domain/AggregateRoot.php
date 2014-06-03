<?php

namespace CQRS\Domain;

interface AggregateRoot
{
    public function pullDomainEvents();
} 
