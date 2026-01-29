package com.darija.translator.security;

import jakarta.enterprise.context.ApplicationScoped;
import jakarta.security.enterprise.credential.Credential;
import jakarta.security.enterprise.credential.UsernamePasswordCredential;
import jakarta.security.enterprise.identitystore.CredentialValidationResult;
import jakarta.security.enterprise.identitystore.IdentityStore;
import java.util.HashSet;
import java.util.Set;

@ApplicationScoped
public class InMemoryIdentityStore implements IdentityStore {

    @Override
    public CredentialValidationResult validate(Credential credential) {
        if (credential instanceof UsernamePasswordCredential) {
            UsernamePasswordCredential usernamePassword = (UsernamePasswordCredential) credential;
            String username = usernamePassword.getCaller();
            String password = usernamePassword.getPasswordAsString();

            if ("admin".equals(username) && "admin123".equals(password)) {
                Set<String> roles = new HashSet<>();
                roles.add("admin");
                return new CredentialValidationResult(username, roles);
            }
            
            if ("user".equals(username) && "user123".equals(password)) {
                Set<String> roles = new HashSet<>();
                roles.add("user");
                return new CredentialValidationResult(username, roles);
            }
        }

        return CredentialValidationResult.INVALID_RESULT;
    }
}