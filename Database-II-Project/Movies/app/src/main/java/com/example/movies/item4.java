package com.example.phaseIII;

import android.graphics.Color;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.example.PhaseIII.R;
import com.example.phaseIII.models.Responses;
import com.example.phaseIII.network.ApiService;
import com.example.phaseIII.network.RetrofitClient;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class item4 extends AppCompatActivity {
    
    ApiService apiService;
    EditText editInstructorId, editPassword;
    TextView tvMessage;
    Button btnViewRecords;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.item4);
        apiService = RetrofitClient.getInstance().create(ApiService.class);
        initializeViews();
        setButtonListeners();
    }

    private void initializeViews() {
        editInstructorId = findViewById(R.id.editInstructorId);
        editPassword = findViewById(R.id.editPassword);
        tvMessage = findViewById(R.id.tvMessage);
        btnViewRecords = findViewById(R.id.btnViewRecords);
    }

    private void setButtonListeners() {
        btnViewRecords.setOnClickListener(v -> viewRecords());
    }

    private void viewRecords() {
        String instructorId = editInstructorId.getText().toString().trim();
        String password = editPassword.getText().toString().trim();

        if (!isFormValid(instructorId, password)) return;

        Call<Responses> call = apiService.viewRecords(instructorId, password);
        call.enqueue(new Callback<Responses>() {
            @Override
            public void onResponse(Call<Responses> call, Response<Responses> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Responses result = response.body();
                    int color = result.isSuccess() ? Color.GREEN : result.isReply() ? Color.BLACK : Color.RED;
                    showMessage(result.getMessage(), color);
                } else {
                    showMessage("Unexpected server response.", Color.RED);
                }
            }

            @Override
            public void onFailure(Call<Responses> call, Throwable t) {
                showMessage("Failed to connect: " + t.getMessage(), Color.RED);
            }
        });
    }

    private boolean isFormValid(String instructorId, String password) {
        if (instructorId.isEmpty()) {
            showMessage("Please enter instructor ID.", Color.RED);
            return false;
        }
        if (password.isEmpty()) {
            showMessage("Please enter password.", Color.RED);
            return false;
        }
        return true;
    }

    private void showMessage(String message, int color) {
        tvMessage.setTextColor(color);
        tvMessage.setText(message);
    }
}
