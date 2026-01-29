package com.darija.translator.security;

import jakarta.enterprise.context.ApplicationScoped;
import jakarta.security.enterprise.authentication.mechanism.http.BasicAuthenticationMechanismDefinition;

@BasicAuthenticationMechanismDefinition(realmName = "file")
@ApplicationScoped
public class SecurityConfig {
}