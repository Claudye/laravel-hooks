<?php

namespace LaravelHooks\Traits;

trait HasModelEventHooks
{
    /**
     * Liste des événements Eloquent et leur méthode associée.
     */
    protected static $modelEvents = [
        'retrieved',
        'creating',
        'created',
        'updating',
        'updated',
        'saving',
        'saved',
        'deleting',
        'deleted',
        'trashed',
        'forceDeleting',
        'forceDeleted',
        'restoring',
        'restored',
        'replicating',
    ];

    /**
     * Préfixe par défaut pour les méthodes d'événements.
     */
    protected static $defaultEventMethodPrefix = 'on';

    /**
     * Boot the trait and attach the model event hooks with a dynamic method prefix.
     */
    protected static function bootHasModelEventHooks()
    {
        foreach (static::$modelEvents as $event) {

            if (method_exists(static::class, $event)) {
                static::$event(function ($model) use ($event) {
                    $prefix = $model->getEventMethodPrefix();
                    $method = $prefix . ucfirst($event);

                    // Si la méthode existe dans le modèle, elle est appelée
                    if (method_exists($model, $method)) {
                        $model->{$method}($model);
                    }
                });
            }
        }
    }

    /**
     * Récupérer le préfixe des méthodes d'événements.
     * 
     * @return string
     */
    public function getEventMethodPrefix()
    {
        // Si le modèle a défini un préfixe, on l'utilise, sinon on prend le préfixe par défaut
        return property_exists($this, 'eventMethodPrefix') ? $this->eventMethodPrefix : static::$defaultEventMethodPrefix;
    }
}
