package com.example.movies.models;

import java.util.List;

public class Responses {
    private boolean success;
    private boolean reply;
    private String message;

    public List<String> clubs;
    
    public boolean isSuccess() {
        return success;
    }

    public boolean isReply() {
        return reply;
    }

    public String getMessage() {
        return message;
    }
}