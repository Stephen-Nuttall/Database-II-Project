package com.example.movies.models;

import java.util.List;

public class RecordsResponse {
    private boolean success;
    private boolean reply;
    private String message;
    
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