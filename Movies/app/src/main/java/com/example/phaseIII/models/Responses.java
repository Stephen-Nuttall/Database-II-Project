package com.example.movies.models;

import java.util.List;

public class Responses {
    private boolean success;
    private boolean reply;
    private String message;

    public List<String> sections;
    public List<String> clubs;

    public String semester;
    public String year;
    
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