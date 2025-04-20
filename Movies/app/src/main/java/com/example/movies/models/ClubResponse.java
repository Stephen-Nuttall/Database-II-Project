package com.example.movies.models;

import java.util.List;

public class ClubResponse {
    private boolean success;
    private String message;

    public List<String> clubs;

    public boolean isSuccess() {
        return success;
    }

    public String getMessage() {
        return message;
    }
}
