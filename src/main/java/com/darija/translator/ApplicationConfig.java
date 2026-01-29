package com.darija.translator;

import jakarta.ws.rs.ApplicationPath;
import jakarta.ws.rs.core.Application;

/**
 * Configuration minimale JAX-RS
 * Active l'API REST sur le chemin /api
 */
@ApplicationPath("/")
public class ApplicationConfig extends Application {
    // Configuration JAX-RS de base
    // Jersey découvre automatiquement les ressources via package scanning
}