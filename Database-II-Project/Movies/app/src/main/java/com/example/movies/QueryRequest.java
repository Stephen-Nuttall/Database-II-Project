package com.example.phaseIII;

import android.util.Log;

import com.android.volley.AuthFailureError;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;

import java.util.HashMap;
import java.util.Map;

public class QueryRequest extends StringRequest {
    private Map<String, String> args;
    private static Response.ErrorListener err = new Response.ErrorListener(){
        @Override
        public void onErrorResponse(VolleyError error){
            Log.d("please","Error listener response: " + error.getMessage());
        }
    };

    public QueryRequest(String[] argNames, String[] arguments, String url, Response.Listener<String> listener){
        super(Method.POST, url, listener, err);
        args = new HashMap<String, String>();
        for (int i = 0; i < arguments.length; i++) {
            args.put(argNames[i], arguments[i]);
        }
    }

    @Override
    protected Map<String, String> getParams() throws AuthFailureError {
        return args;
    }
}